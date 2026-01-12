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
from fastapi import FastAPI, UploadFile, File, Form, HTTPException, Depends
from fastapi.responses import JSONResponse
from pydantic import BaseModel
import uvicorn
from typing import Union, List
from datetime import datetime
from sqlalchemy import Column, Integer, String, Text, JSON, DateTime, Boolean, UniqueConstraint
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session

# ==================== KẾT NỐI MYSQL ====================
DATABASE_URL = "mysql+pymysql://root:@localhost:3306/job_matcher"

engine = create_engine(
    DATABASE_URL,
    echo=False,
    pool_pre_ping=True,
    pool_recycle=3600
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# ==================== MODEL CŨ ====================
class CrawlRun(Base):
    __tablename__ = "crawl_runs"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, nullable=True)
    group_id = Column(Integer, nullable=True)
    source = Column(String(100), nullable=True)
    status = Column(String(50), default="running")
    parameters = Column(JSON, nullable=True)
    jobs_crawled = Column(Integer, default=0)
    error_message = Column(Text, nullable=True)
    detail = Column(Text, nullable=True)
    result = Column(JSON, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

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

# ==================== MODEL MỚI: QUẢN LÝ SELECTOR ====================
class SiteSelector(Base):
    __tablename__ = "site_selectors"

    id = Column(Integer, primary_key=True, index=True)
    site = Column(String(50), nullable=False, index=True)              # ví dụ: "topcv"
    page_type = Column(String(50), nullable=False)                     # search_form, job_list, job_detail
    element_key = Column(String(100), nullable=False)                  # tên key để code gọi
    selector_type = Column(String(20), default="xpath")                # xpath, css, id, class
    selector_value = Column(Text, nullable=False)                      # giá trị selector thực tế
    description = Column(Text, nullable=True)                          # mô tả chức năng
    is_active = Column(Boolean, default=True)
    version = Column(Integer, default=1)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    created_at = Column(DateTime, default=datetime.utcnow)

    __table_args__ = (UniqueConstraint('site', 'page_type', 'element_key', name='uq_site_page_element'),)

# Tạo tất cả các bảng
Base.metadata.create_all(bind=engine)

# ==================== DEPENDENCY ====================
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ==================== HÀM LẤY SELECTOR TỪ DB ====================
def get_selector(db: Session, site: str, page_type: str, element_key: str) -> tuple:
    """
    Trả về (By.TYPE, selector_value)
    Ví dụ: (By.ID, "keyword") hoặc (By.XPATH, "//button[@type='submit']")
    """
    record = db.query(SiteSelector).filter(
        SiteSelector.site == site,
        SiteSelector.page_type == page_type,
        SiteSelector.element_key == element_key,
        SiteSelector.is_active == True
    ).order_by(SiteSelector.version.desc()).first()

    if not record:
        raise ValueError(f"Không tìm thấy selector active: {site} - {page_type} - {element_key}")

    selector_type = record.selector_type.lower()
    if selector_type == "id":
        return By.ID, record.selector_value
    elif selector_type == "css":
        return By.CSS_SELECTOR, record.selector_value
    elif selector_type == "class":
        return By.CLASS_NAME, record.selector_value
    else:  # xpath hoặc mặc định
        return By.XPATH, record.selector_value

# ==================== CÁC MODEL RESPONSE ====================
class MatchWithJobsResult(BaseModel):
    title: str
    salary: str
    experience: Union[int, str]
    location: str
    score: float
    matching_skills: str
    missing_skills: str
    url: str

class MatchResult(BaseModel):
    title: str
    salary: str
    experience: Union[int, str]
    location: str
    score: float
    matching_skills: str
    url: str

# ==================== FASTAPI APP ====================
app = FastAPI(title="Semantic CV Matcher + TopCV Job Crawler API")

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

# ==================== HÀM CRAWL TOPCV (SỬ DỤNG SELECTOR TỪ DB) ====================
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

def safe_get_text(driver, by, selector, timeout: int = 5) -> str:
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.presence_of_element_located((by, selector))
        )
        return element.text.strip()
    except:
        return ""

def safe_click_js(driver, by, selector, timeout: int = 10) -> bool:
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.element_to_be_clickable((by, selector))
        )
        driver.execute_script("arguments[0].click();", element)
        return True
    except:
        return False

def crawl_topcv(keyword: str = None, location: str = None, level: str = None, salary: str = None, search_range: int = 20) -> list:
    driver = None
    db = next(get_db())  # lấy session mới
    all_results = []
    try:
        driver = create_driver(headless=False)
        wait = WebDriverWait(driver, 15)

        driver.get("https://www.topcv.vn/viec-lam-it")
        time.sleep(4)

        # === Tìm kiếm form ===
        if keyword:
            by, sel = get_selector(db, "topcv", "search_form", "keyword_input")
            kw_input = wait.until(EC.element_to_be_clickable((by, sel)))
            kw_input.clear()
            kw_input.send_keys(keyword)
            time.sleep(1)

        if location and location != "Tất cả tỉnh/thành phố":
            by, sel = get_selector(db, "topcv", "search_form", "city_dropdown")
            city_box = wait.until(EC.element_to_be_clickable((by, sel)))
            city_box.click()
            time.sleep(1)
            city_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and normalize-space(text())='{location}']")))
            city_option.click()
            time.sleep(1)

        if level and level != "Tất cả cấp bậc":
            by, sel = get_selector(db, "topcv", "search_form", "position_dropdown")
            level_box = wait.until(EC.element_to_be_clickable((by, sel)))
            level_box.click()
            time.sleep(1)
            level_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and contains(normalize-space(text()), '{level}')]")))
            level_option.click()
            time.sleep(1)

        if salary and salary != "Tất cả mức lương":
            by, sel = get_selector(db, "topcv", "search_form", "salary_dropdown")
            salary_box = wait.until(EC.element_to_be_clickable((by, sel)))
            salary_box.click()
            time.sleep(1)
            salary_option = wait.until(EC.element_to_be_clickable((By.XPATH, f"//li[contains(@class,'select2-results__option') and contains(normalize-space(text()), '{salary}')]")))
            salary_option.click()
            time.sleep(1)

        by, sel = get_selector(db, "topcv", "search_form", "search_button")
        search_btn = wait.until(EC.element_to_be_clickable((by, sel)))
        driver.execute_script("arguments[0].click();", search_btn)
        time.sleep(5)

        main_window = driver.current_window_handle
        crawled_count = 0
        current_position = 1

        # Lấy selector cho job card container và title link
        by_container, sel_container = get_selector(db, "topcv", "job_list", "job_card_container")
        by_title, sel_title_relative = get_selector(db, "topcv", "job_list", "job_title_link")

        while crawled_count < search_range:
            try:
                # Tạo XPath đầy đủ cho job thứ current_position
                job_card_xpath = f"({sel_container})[{current_position}]"
                job_link_xpath = f"{job_card_xpath}{sel_title_relative}"

                job_link = WebDriverWait(driver, 8).until(
                    EC.presence_of_element_located((By.XPATH, job_link_xpath))
                )

                driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", job_link)
                job_url = job_link.get_attribute("href")
                driver.execute_script("window.open(arguments[0], '_blank');", job_url)

                WebDriverWait(driver, 10).until(lambda d: len(d.window_handles) > 1)
                driver.switch_to.window(driver.window_handles[-1])
                time.sleep(3)

                # Click các nút mở rộng (nếu có)
                try:
                    by_chk, sel_chk = get_selector(db, "topcv", "job_detail", "checkbox_agree")
                    safe_click_js(driver, by_chk, sel_chk, timeout=3)
                except:
                    pass
                try:
                    by_btn, sel_btn = get_selector(db, "topcv", "job_detail", "expand_button")
                    safe_click_js(driver, by_btn, sel_btn, timeout=3)
                except:
                    pass

                # Lấy thông tin chi tiết
                job_data = {
                    "title": safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "title")),
                    "salary_raw": safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "salary")),
                    "location": safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "location")),
                    "experience": safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "experience")),
                    "job_description": [safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "job_description"))],
                    "requirements": [safe_get_text(driver, *get_selector(db, "topcv", "job_detail", "requirements"))],
                    "url": driver.current_url
                }

                all_results.append(job_data)
                crawled_count += 1

                driver.close()
                driver.switch_to.window(main_window)
                current_position += 1

            except TimeoutException:
                # Chuyển trang
                try:
                    by_next, sel_next = get_selector(db, "topcv", "job_list", "next_page_button")
                    next_btn = wait.until(EC.element_to_be_clickable((by_next, sel_next)))
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

# ==================== CÁC HÀM KHÁC (GIỮ NGUYÊN) ====================
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
    jobs_data: str = Form(...),
    run_id: int = Form(...),
    extra_skills: str = Form(""),
    desired_position: str = Form("")
):
    db = SessionLocal()
    try:
        try:
            jobs = json.loads(jobs_data)
        except json.JSONDecodeError:
            raise HTTPException(status_code=400, detail="jobs_data phải là chuỗi JSON hợp lệ.")

        if not jobs or not isinstance(jobs, list):
            raise HTTPException(status_code=400, detail="Danh sách job rỗng hoặc không hợp lệ.")

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

            if "job_description" in job:
                if isinstance(job["job_description"], list):
                    desc_list.extend(job["job_description"])
                elif isinstance(job["job_description"], str):
                    desc_list.append(job["job_description"])

            if "requirements" in job:
                if isinstance(job["requirements"], list):
                    req_list.extend(job["requirements"])
                elif isinstance(job["requirements"], str):
                    req_list.append(job["requirements"])

            full_desc = " ".join([str(d) for d in (desc_list + req_list) if d])
            if not full_desc.strip():
                continue

            jd_text = clean_text(full_desc)
            jd_emb = model.encode(jd_text, convert_to_tensor=True)

            cos_score = util.cos_sim(cv_emb, jd_emb).item() * 100

            skills_jd = extract_skills(jd_text)
            intersection = skills_cv & skills_jd
            missing_in_cv = skills_jd - skills_cv

            union = skills_cv | skills_jd
            jaccard = len(intersection) / len(union) if union else 0

            final_score = 0.7 * cos_score + 0.3 * (jaccard * 100)

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

            exp = job.get("experience", {})
            if isinstance(exp, dict):
                experience_val = exp.get("min_years", "Không yêu cầu")
            else:
                experience_val = job.get("experience", "Không yêu cầu")
            
            experience_str = int(experience_val) if str(experience_val).isdigit() else experience_val

            missing_str = ", ".join(sorted(missing_in_cv)[:8]) if missing_in_cv else "Không thiếu kỹ năng nào"

            results.append({
                "title": job.get("title", "Không rõ"),
                "salary": salary_str,
                "experience": experience_str,
                "location": job.get("location", "Không xác định"),
                "score": round(final_score, 1),
                "matching_skills": ", ".join(sorted(intersection)) if intersection else "Không có",
                "missing_skills": missing_str,
                "url": job.get("url", "#")
            })

        results.sort(key=lambda x: x["score"], reverse=True)

        # Lưu kết quả vào DB
        crawl_run = db.query(CrawlRun).filter(CrawlRun.id == run_id).first()
        if not crawl_run:
            raise HTTPException(status_code=404, detail=f"Không tìm thấy crawl run với id = {run_id}")

        crawl_run.result = results
        db.commit()

        return results

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi không xác định: {str(e)}")
    finally:
        db.close()

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)