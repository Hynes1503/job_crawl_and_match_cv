import json
import re
from io import BytesIO
import PyPDF2
import docx
import pandas as pd
from sentence_transformers import SentenceTransformer, util
import os
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.responses import JSONResponse
from pydantic import BaseModel
import uvicorn
from typing import Union, List

# ==================== THÊM KẾT NỐI MYSQL (CỔNG 3307) ====================
from sqlalchemy import create_engine, Column, Integer, String, Text, JSON
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# THAY ĐỔI THÔNG TIN NÀY THEO MÁY BẠN
DATABASE_URL = "mysql+pymysql://root:@localhost:3307/job_matcher"

engine = create_engine(
    DATABASE_URL,
    echo=False,                  # Để True nếu muốn xem SQL log
    pool_pre_ping=True,
    pool_recycle=3600
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# ==================== THÊM MODEL CHO BẢNG crawl_runs ====================
class CrawlRun(Base):
    __tablename__ = "crawl_runs"

    id = Column(Integer, primary_key=True, index=True)
    keyword = Column(String(255))
    location = Column(String(255))
    level = Column(String(255))
    salary = Column(String(255))
    search_range = Column(Integer)
    jobs_data = Column(JSON)           # Danh sách job thô hoặc cleaned
    result = Column(JSON, nullable=True)  # Kết quả matching CV, lưu dưới dạng JSON
    created_at = Column(String(50))     # Hoặc dùng DateTime nếu muốn chuẩn hơn

# Đảm bảo tạo table (nếu chưa có)
Base.metadata.create_all(bind=engine)

# Thêm model mới cho response của endpoint này (tùy chọn, để có docs rõ ràng hơn)
class MatchWithJobsResult(BaseModel):
    title: str
    salary: str
    experience: Union[int, str]
    location: str
    score: float
    matching_skills: str
    url: str

# Định nghĩa model Job (sau này có thể dùng để lưu DB)
class JobDB(Base):
    __tablename__ = "jobs"

    id = Column(Integer, primary_key=True, index=True)
    title = Column(String(255), nullable=False)
    job_description = Column(JSON)
    requirements = Column(JSON)
    salary = Column(JSON)
    location = Column(String(255))
    experience_min_years = Column(String(50))
    url = Column(Text, unique=True)

# Tạo table nếu chưa tồn tại (chỉ chạy 1 lần khi khởi động app)
Base.metadata.create_all(bind=engine)

# Hàm helper để lấy session (dùng khi bạn muốn lưu DB sau này)
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ===================================================================

app = FastAPI(title="Semantic CV Matcher + TopCV Job Crawler API")

# ==================== THƯ MỤC ====================
CLEAN_DIR = "clean"
RAW_DIR = "crawl"
os.makedirs(CLEAN_DIR, exist_ok=True)
os.makedirs(RAW_DIR, exist_ok=True)

RAW_JOBS_FILE = os.path.join(RAW_DIR, "jobs_detail.json")
CLEANED_JOBS_FILE = os.path.join(CLEAN_DIR, "cleaned_jobs.json")

# ==================== HÀM HỖ TRỢ TRÍCH XUẤT & LÀM SẠCH ====================
def extract_text_from_pdf(file_bytes: bytes) -> str:
    try:
        reader = PyPDF2.PdfReader(BytesIO(file_bytes))
        text = ""
        for page in reader.pages:
            page_text = page.extract_text()
            if page_text:
                text += page_text + "\n"
        return text
    except:
        return ""

def extract_text_from_docx(file_bytes: bytes) -> str:
    try:
        doc = docx.Document(BytesIO(file_bytes))
        return "\n".join([para.text for para in doc.paragraphs])
    except:
        return ""

def extract_text_from_file(file_content: bytes, file_type: str) -> str:
    if file_type == "application/pdf":
        return extract_text_from_pdf(file_content)
    elif file_type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
        return extract_text_from_docx(file_content)
    elif file_type == "text/plain":
        return file_content.decode("utf-8")
    else:
        return ""

def clean_text(text: str) -> str:
    if not text:
        return ""
    text = re.sub(r"\s+", " ", text)
    text = re.sub(r"[^\w\s]", " ", text.lower())
    return text.strip()

# ==================== HÀM TRÍCH XUẤT KỸ NĂNG ====================
def extract_skills(text: str) -> set:
    common_skills = [
        'python', 'java', 'sql', 'react', 'aws', 'docker', 'machine learning', 'data analysis',
        'javascript', 'c++', 'nodejs', 'mongodb', 'git', 'agile', 'scrum', 'cloud', 'devops',
        'kubernetes', 'tensorflow', 'pytorch', 'pandas', 'numpy', 'scikit-learn', 'excel',
        'tableau', 'power bi', 'hadoop', 'spark', 'kafka', 'linux', 'unix', 'rest api',
        'microservices', 'angular', 'vue.js', 'html', 'css', 'typescript', 'php', 'ruby',
        'go', 'swift', 'kotlin', 'flutter', 'react native', 'ios', 'android', 'cyber security',
        'networking', 'database', 'oracle', 'mysql', 'postgresql', 'nosql', 'big data', 'ai',
        'nlp', 'computer vision', 'statistics', 'probability', 'calculus', 'linear algebra'
    ]
    
    found_skills = set()
    text_lower = text.lower()
    for skill in common_skills:
        if re.search(r'\b' + re.escape(skill) + r'\b', text_lower):
            found_skills.add(skill)
    
    return found_skills

# ==================== HÀM CRAWL TOPCV ====================
def create_driver(headless: bool = True):
    chrome_options = Options()
    if headless:
        chrome_options.add_argument("--headless=new")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    driver = webdriver.Chrome(options=chrome_options)
    return driver

def safe_get_text(driver, xpath: str, timeout: int = 5) -> str:
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.presence_of_element_located((By.XPATH, xpath))
        )
        return element.text.strip()
    except:
        return ""

def safe_click_js(driver, xpath: str, timeout: int = 10) -> bool:
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.element_to_be_clickable((By.XPATH, xpath))
        )
        driver.execute_script("arguments[0].click();", element)
        return True
    except:
        return False

def crawl_topcv(keyword: str = None, location: str = None, level: str = None, salary: str = None, search_range: int = 20) -> list:
    driver = None
    all_results = []
    try:
        driver = create_driver(headless=True)
        wait = WebDriverWait(driver, 15)

        driver.get("https://www.topcv.vn/viec-lam-it")
        time.sleep(4)

        if keyword:
            kw_input = wait.until(EC.element_to_be_clickable((By.ID, "keyword")))
            kw_input.clear()
            kw_input.send_keys(keyword)
            time.sleep(1)

        if location and location != "Tất cả tỉnh/thành phố":
            city_box = wait.until(EC.element_to_be_clickable((By.ID, "select2-city-container")))
            city_box.click()
            time.sleep(1)
            city_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and normalize-space(text())='{location}']")))
            city_option.click()
            time.sleep(1)

        if level and level != "Tất cả cấp bậc":
            level_box = wait.until(EC.element_to_be_clickable((By.ID, "select2-position-container")))
            level_box.click()
            time.sleep(1)
            level_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and contains(normalize-space(text()), '{level}')]")))
            level_option.click()
            time.sleep(1)

        if salary and salary != "Tất cả mức lương":
            salary_box = wait.until(EC.element_to_be_clickable((By.ID, "select2-salary-container")))
            salary_box.click()
            time.sleep(1)
            salary_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and contains(normalize-space(text()), '{salary}')]")))
            salary_option.click()
            time.sleep(1)

        search_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//form[@id='frm-search-job']//button[@type='submit']")))
        driver.execute_script("arguments[0].click();", search_btn)
        time.sleep(5)

        main_window = driver.current_window_handle
        crawled_count = 0
        current_position = 1

        while crawled_count < search_range:
            job_xpath = f'//*[@id="main"]/div[1]/div[3]/div[4]/div[1]/div[1]/div[{current_position}]/div/div[2]/div[2]/h3/a'
            try:
                job_link = WebDriverWait(driver, 8).until(
                    EC.presence_of_element_located((By.XPATH, job_xpath))
                )

                driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", job_link)
                job_url = job_link.get_attribute("href")
                driver.execute_script("window.open(arguments[0], '_blank');", job_url)

                WebDriverWait(driver, 10).until(lambda d: len(d.window_handles) > 1)
                driver.switch_to.window(driver.window_handles[-1])
                time.sleep(3)

                safe_click_js(driver, '//*[@id="SoGDz7"]/div/label/input', timeout=3)
                safe_click_js(driver, '//*[@id="box-job-information-detail"]/div[4]/button', timeout=3)

                job_data = {
                    "title": safe_get_text(driver, '//*[@id="header-job-info"]/h1'),
                    "salary_raw": safe_get_text(driver, '//*[@id="header-job-info"]/div[1]/div[1]/div[2]/div[2]'),
                    "location": safe_get_text(driver, '//*[@id="header-job-info"]/div[1]/div[2]/div[2]/div[2]'),
                    "experience": safe_get_text(driver, '//*[@id="job-detail-info-experience"]/div[2]/div[2]'),
                    "job_description": [safe_get_text(driver, '//*[@id="box-job-information-detail"]/div[3]/div[1]/div/div[1]/div')],
                    "requirements": [safe_get_text(driver, '//*[@id="box-job-information-detail"]/div[3]/div[1]/div/div[2]/div')],
                    "url": driver.current_url
                }

                all_results.append(job_data)
                crawled_count += 1

                driver.close()
                driver.switch_to.window(main_window)
                current_position += 1

            except TimeoutException:
                try:
                    next_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(@class, 'next') or contains(text(), 'Trang tiếp theo')]")))
                    driver.execute_script("arguments[0].click();", next_btn)
                    time.sleep(5)
                    current_position = 1
                except:
                    break
            except Exception:
                current_position += 1
                continue

        with open(RAW_JOBS_FILE, "w", encoding="utf-8") as f:
            json.dump(all_results, f, ensure_ascii=False, indent=2)

        return all_results

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi crawl: {str(e)}")
    finally:
        if driver:
            driver.quit()

# ==================== LÀM SẠCH DỮ LIỆU ====================
def is_valid_text_list(value):
    if not isinstance(value, list):
        return False
    for item in value:
        if isinstance(item, str) and item.strip():
            return True
    return False

def convert_raw_to_clean() -> list:
    if not os.path.exists(RAW_JOBS_FILE):
        return []

    with open(RAW_JOBS_FILE, "r", encoding="utf-8") as f:
        raw_jobs = json.load(f)

    cleaned_jobs = []

    for job in raw_jobs:
        job_description = job.get("job_description")
        requirements = job.get("requirements")

        if not is_valid_text_list(job_description):
            continue
        if not is_valid_text_list(requirements):
            continue

        salary_text = job.get("salary_raw", "").lower()
        salary = {}
        if "thoả thuận" in salary_text or "đàm phán" in salary_text or not salary_text:
            pass
        else:
            numbers = re.findall(r"\d+", salary_text.replace(",", "").replace(".", ""))
            if len(numbers) >= 2:
                salary["min"] = int(numbers[0])
                salary["max"] = int(numbers[1])
            elif len(numbers) == 1:
                salary["max"] = int(numbers[0])

        exp_text = job.get("experience", "").lower()
        min_years = "Không yêu cầu"
        if "năm" in exp_text:
            nums = re.findall(r"\d+", exp_text)
            if nums:
                min_years = int(nums[0])

        cleaned = {
            "title": job.get("title", "Không rõ"),
            "job_description": job_description,
            "requirements": requirements,
            "salary": salary,
            "location": job.get("location", "Không xác định"),
            "experience": {"min_years": min_years},
            "url": job.get("url", "#")
        }

        cleaned_jobs.append(cleaned)

    with open(CLEANED_JOBS_FILE, "w", encoding="utf-8") as f:
        json.dump(cleaned_jobs, f, ensure_ascii=False, indent=2)

    return cleaned_jobs

# ==================== LOAD MODEL & JOBS ====================
model = SentenceTransformer('all-MiniLM-L6-v2')

def load_jobs() -> list:
    if os.path.exists(CLEANED_JOBS_FILE):
        try:
            with open(CLEANED_JOBS_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
            return [job for job in data if job.get("title")]
        except:
            return []
    return []

# ==================== ENDPOINTS ====================
@app.post("/crawl")
async def crawl_jobs(
    keyword: str = Form(None),
    location: str = Form(None),
    level: str = Form(None),
    salary: str = Form(None),
    search_range: int = Form(20)
):
    if search_range > 50:
        raise HTTPException(status_code=400, detail="search_range tối đa 50")
    
    raw_results = crawl_topcv(
        keyword=keyword,
        location=location,
        level=level,
        salary=salary,
        search_range=search_range
    )
    
    if raw_results:
        cleaned_jobs = convert_raw_to_clean()
        return {"message": f"Hoàn thành! Đã crawl {len(raw_results)} công việc.", "jobs_count": len(cleaned_jobs)}
    else:
        raise HTTPException(status_code=500, detail="Không crawl được job nào.")

class MatchResult(BaseModel):
    title: str
    salary: str
    experience: Union[int, str]
    location: str
    score: float
    matching_skills: str
    url: str

@app.post("/match", response_model=list[MatchResult])
async def match_cv(
    cv_file: UploadFile = File(...),
    extra_skills: str = Form(""),
    desired_position: str = Form("")
):
    jobs = load_jobs()
    if not jobs:
        raise HTTPException(status_code=400, detail="Chưa có dữ liệu job. Hãy crawl trước.")

    content = await cv_file.read()
    file_type = cv_file.content_type
    raw_cv = extract_text_from_file(content, file_type)
    
    if not raw_cv.strip():
        raise HTTPException(status_code=400, detail="Không đọc được nội dung CV!")

    extra_text = f"\nKỹ năng: {extra_skills}\nVị trí mong muốn: {desired_position}"
    cv_text = clean_text(raw_cv + extra_text)
    cv_emb = model.encode(cv_text, convert_to_tensor=True)

    skills_cv = extract_skills(cv_text)

    results = []
    for job in jobs:
        desc_list = job.get("job_description", []) + job.get("requirements", [])
        full_desc = " ".join(desc_list)
        jd_text = clean_text(full_desc)
        jd_emb = model.encode(jd_text, convert_to_tensor=True)

        cos_score = util.cos_sim(cv_emb, jd_emb).item() * 100

        skills_jd = extract_skills(jd_text)
        intersection = skills_cv & skills_jd
        union = skills_cv | skills_jd
        jaccard = len(intersection) / len(union) if union else 0

        final_score = 0.7 * cos_score + 0.3 * (jaccard * 100)

        salary_info = job.get("salary", {})
        if salary_info.get("min") and salary_info.get("max"):
            salary_str = f"{salary_info['min']} - {salary_info['max']} triệu VND"
        elif salary_info.get("max"):
            salary_str = f"Lên đến {salary_info['max']} triệu VND"
        else:
            salary_str = "Thoả thuận"

        results.append({
            "title": job.get("title", ""),
            "salary": salary_str,
            "experience": job.get("experience", {}).get("min_years", "Không yêu cầu"),
            "location": job.get("location", "Không xác định"),
            "score": round(final_score, 2),
            "matching_skills": ", ".join(sorted(intersection)) if intersection else "Không có",
            "url": job.get("url", "#")
        })

    results.sort(key=lambda x: x["score"], reverse=True)
    
    return results

@app.get("/jobs")
async def get_jobs():
    jobs = load_jobs()
    return {"jobs_count": len(jobs), "jobs": jobs}

@app.post("/match-with-jobs", response_model=List[MatchWithJobsResult])
async def match_with_jobs(
    cv_file: UploadFile = File(...),
    jobs_data: str = Form(...),                  # Chuỗi JSON jobs từ client
    run_id: int = Form(...),                     # THÊM: ID của crawl run để lưu kết quả
    extra_skills: str = Form(""),
    desired_position: str = Form("")
):
    """
    Matching CV với danh sách job được truyền trực tiếp.
    Đồng thời lưu kết quả matching vào cột result của bảng crawl_runs theo run_id.
    """
    db = SessionLocal()
    try:
        # Parse jobs_data
        try:
            jobs = json.loads(jobs_data)
        except json.JSONDecodeError:
            raise HTTPException(status_code=400, detail="jobs_data phải là chuỗi JSON hợp lệ.")

        if not jobs or not isinstance(jobs, list):
            raise HTTPException(status_code=400, detail="Danh sách job rỗng hoặc không hợp lệ.")

        # Đọc và xử lý CV
        content = await cv_file.read()
        file_type = cv_file.content_type
        raw_cv = extract_text_from_file(content, file_type)
        
        if not raw_cv.strip():
            raise HTTPException(status_code=400, detail="Không đọc được nội dung CV!")

        extra_text = f"\nKỹ năng bổ sung: {extra_skills}\nVị trí mong muốn: {desired_position}"
        cv_text = clean_text(raw_cv + extra_text)
        cv_emb = model.encode(cv_text, convert_to_tensor=True)
        skills_cv = extract_skills(cv_text)

        results = []

        for job in jobs:
            desc_list = []
            req_list = []

            if "job_description" in job and isinstance(job["job_description"], list):
                desc_list.extend(job["job_description"])
            elif "job_description" in job and isinstance(job["job_description"], str):
                desc_list.append(job["job_description"])

            if "requirements" in job and isinstance(job["requirements"], list):
                req_list.extend(job["requirements"])
            elif "requirements" in job and isinstance(job["requirements"], str):
                req_list.append(job["requirements"])

            full_desc = " ".join(desc_list + req_list)
            if not full_desc.strip():
                continue

            jd_text = clean_text(full_desc)
            jd_emb = model.encode(jd_text, convert_to_tensor=True)

            cos_score = util.cos_sim(cv_emb, jd_emb).item() * 100

            skills_jd = extract_skills(jd_text)
            intersection = skills_cv & skills_jd
            union = skills_cv | skills_jd
            jaccard = len(intersection) / len(union) if union else 0

            final_score = 0.7 * cos_score + 0.3 * (jaccard * 100)

            # Xử lý lương
            salary_data = job.get("salary", {})
            if isinstance(salary_data, dict):
                if salary_data.get("min") and salary_data.get("max"):
                    salary_str = f"{salary_data['min']} - {salary_data['max']} triệu"
                elif salary_data.get("max"):
                    salary_str = f"Đến {salary_data['max']} triệu"
                else:
                    salary_str = "Thoả thuận"
            else:
                salary_str = job.get("salary_raw", "Thoả thuận") or "Thoả thuận"

            # Xử lý kinh nghiệm
            exp = job.get("experience", {})
            if isinstance(exp, dict):
                experience_val = exp.get("min_years", "Không yêu cầu")
            else:
                experience_val = job.get("experience", "Không yêu cầu")
            
            if isinstance(experience_val, int) or (isinstance(experience_val, str) and experience_val.isdigit()):
                experience_str = int(experience_val)
            else:
                experience_str = experience_val

            results.append({
                "title": job.get("title", "Không rõ"),
                "salary": salary_str,
                "experience": experience_str,
                "location": job.get("location", "Không xác định"),
                "score": round(final_score, 1),
                "matching_skills": ", ".join(sorted(intersection)) if intersection else "Không có",
                "url": job.get("url", "#")
            })

        # Sắp xếp theo score giảm dần
        results.sort(key=lambda x: x["score"], reverse=True)

        # ==================== LƯU KẾT QUẢ VÀO DB ====================
        try:
            crawl_run = db.query(CrawlRun).filter(CrawlRun.id == run_id).first()
            if not crawl_run:
                raise HTTPException(status_code=404, detail=f"Không tìm thấy crawl run với id = {run_id}")

            # Lưu toàn bộ danh sách kết quả matching vào cột result (dạng JSON)
            crawl_run.result = results
            db.commit()
        except Exception as db_error:
            db.rollback()
            raise HTTPException(status_code=500, detail=f"Lỗi khi lưu kết quả vào DB: {str(db_error)}")

        # Trả về kết quả cho frontend
        return results

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi không xác định: {str(e)}")
    finally:
        db.close()
        
if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)