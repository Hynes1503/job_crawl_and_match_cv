@extends('layouts.app')

@section('title', 'Dashboard - Job Matcher AI')

@push('styles')
    <style>
        .dashboard-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: calc(100vh - 140px);
            /* Trừ navbar + footer */
            gap: 0;
        }

        .panel {
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .panel-left {
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        .panel-right {
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(12px);
        }

        .panel-title {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 12px;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
            background-size: 200% 200%;
            animation: textGradient 6s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes textGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .panel-desc {
            opacity: 0.85;
            text-align: center;
            max-width: 420px;
            margin-bottom: 32px;
            line-height: 1.5;
            font-size: 1rem;
        }

        .form-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px 14px;
            background: rgba(17, 17, 17, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
        }

        input[type="file"]::file-selector-button {
            background: #fff;
            color: #000;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 12px;
            font-weight: 600;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 999px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn i {
            font-size: 1.3rem;
        }

        .btn-crawl {
            background: #00b4ff;
            color: #fff;
        }

        .btn-crawl:hover {
            background: #00d4ff;
            transform: translateY(-3px);
        }

        .btn-match {
            background: #fff;
            color: #000;
        }

        .btn-match:hover {
            opacity: 0.9;
            transform: translateY(-3px);
        }

        /* Slider */
        .slider-container {
            margin: 20px 0;
        }

        input[type="range"] {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: #00b4ff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 12px rgba(0, 180, 255, 0.6);
        }

        .slider-value {
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: #00ffaa;
            margin-top: 10px;
        }

        /* Kết quả matching */
        .results {
            width: 100%;
            max-width: 460px;
            margin-top: 30px;
        }

        .job-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 16px;
            transition: transform 0.3s;
        }

        .job-card:hover {
            transform: translateY(-4px);
        }

        .job-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .score {
            font-size: 1.8rem;
            font-weight: 800;
            color: #00ffaa;
            margin: 10px 0;
        }

        .info {
            opacity: 0.9;
            line-height: 1.6;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .skills {
            background: rgba(0, 180, 255, 0.15);
            padding: 10px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .apply-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00b4ff;
            color: #fff;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
        }

        .apply-btn:hover {
            background: #00d4ff;
        }

        .status-message {
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            max-width: 420px;
            margin: 20px auto;
        }

        .success {
            background: rgba(0, 255, 150, 0.15);
            border: 1px solid rgba(0, 255, 150, 0.3);
            color: #00ffaa;
        }

        .error {
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.3);
            color: #ff6b6b;
        }

        @media (max-width: 1024px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }

            .panel-left {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            }
        }

        @media (max-width: 768px) {
            .panel {
                padding: 30px 20px;
            }

            .panel-title {
                font-size: 1.8rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-layout">
        <!-- Bên trái: Crawl Jobs -->
        <div class="panel panel-left">
            <h2 class="panel-title">
                <span class="animated-text">Crawl</span><br> Công Việc IT
            </h2>
            <p class="panel-desc">Thu thập dữ liệu việc làm mới nhất từ TopCV</p>

            <div class="form-wrapper">
                <form action="{{ route('crawl.jobs') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Từ khóa (tùy chọn)</label>
                        <input type="text" name="keyword" placeholder="ex: PHP, React, Java..."
                            value="{{ old('keyword') }}">
                    </div>

                    <div class="form-group">
                        <label>Tỉnh/Thành phố</label>
                        <select name="location">
                            <option value="">Tất cả tỉnh/thành phố</option>
                            <option value="Hà Nội">Hà Nội</option>
                            <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                            <option value="Đà Nẵng">Đà Nẵng</option>
                            <option value="Hải Phòng">Hải Phòng</option>
                            <option value="Cần Thơ">Cần Thơ</option>
                            <option value="An Giang">An Giang</option>
                            <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                            <option value="Bắc Giang">Bắc Giang</option>
                            <option value="Bắc Kạn">Bắc Kạn</option>
                            <option value="Bạc Liêu">Bạc Liêu</option>
                            <option value="Bắc Ninh">Bắc Ninh</option>
                            <option value="Bến Tre">Bến Tre</option>
                            <option value="Bình Định">Bình Định</option>
                            <option value="Bình Dương">Bình Dương</option>
                            <option value="Bình Phước">Bình Phước</option>
                            <option value="Bình Thuận">Bình Thuận</option>
                            <option value="Cà Mau">Cà Mau</option>
                            <option value="Cao Bằng">Cao Bằng</option>
                            <option value="Đắk Lắk">Đắk Lắk</option>
                            <option value="Đắk Nông">Đắk Nông</option>
                            <option value="Điện Biên">Điện Biên</option>
                            <option value="Đồng Nai">Đồng Nai</option>
                            <option value="Đồng Tháp">Đồng Tháp</option>
                            <option value="Gia Lai">Gia Lai</option>
                            <option value="Hà Giang">Hà Giang</option>
                            <option value="Hà Nam">Hà Nam</option>
                            <option value="Hà Tĩnh">Hà Tĩnh</option>
                            <option value="Hải Dương">Hải Dương</option>
                            <option value="Hậu Giang">Hậu Giang</option>
                            <option value="Hòa Bình">Hòa Bình</option>
                            <option value="Hưng Yên">Hưng Yên</option>
                            <option value="Khánh Hòa">Khánh Hòa</option>
                            <option value="Kiên Giang">Kiên Giang</option>
                            <option value="Kon Tum">Kon Tum</option>
                            <option value="Lai Châu">Lai Châu</option>
                            <option value="Lâm Đồng">Lâm Đồng</option>
                            <option value="Lạng Sơn">Lạng Sơn</option>
                            <option value="Lào Cai">Lào Cai</option>
                            <option value="Long An">Long An</option>
                            <option value="Nam Định">Nam Định</option>
                            <option value="Nghệ An">Nghệ An</option>
                            <option value="Ninh Bình">Ninh Bình</option>
                            <option value="Ninh Thuận">Ninh Thuận</option>
                            <option value="Phú Thọ">Phú Thọ</option>
                            <option value="Phú Yên">Phú Yên</option>
                            <option value="Quảng Bình">Quảng Bình</option>
                            <option value="Quảng Nam">Quảng Nam</option>
                            <option value="Quảng Ngãi">Quảng Ngãi</option>
                            <option value="Quảng Ninh">Quảng Ninh</option>
                            <option value="Quảng Trị">Quảng Trị</option>
                            <option value="Sóc Trăng">Sóc Trăng</option>
                            <option value="Sơn La">Sơn La</option>
                            <option value="Tây Ninh">Tây Ninh</option>
                            <option value="Thái Bình">Thái Bình</option>
                            <option value="Thái Nguyên">Thái Nguyên</option>
                            <option value="Thanh Hóa">Thanh Hóa</option>
                            <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
                            <option value="Tiền Giang">Tiền Giang</option>
                            <option value="Trà Vinh">Trà Vinh</option>
                            <option value="Tuyên Quang">Tuyên Quang</option>
                            <option value="Vĩnh Long">Vĩnh Long</option>
                            <option value="Vĩnh Phúc">Vĩnh Phúc</option>
                            <option value="Yên Bái">Yên Bái</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cấp bậc</label>
                        <select name="level">
                            <option value="">Tất cả cấp bậc</option>
                            <option value="Thực tập sinh">Thực tập sinh</option>
                            <option value="Nhân viên">Nhân viên</option>
                            <option value="Nhân viên">Trưởng nhóm</option>
                            <option value="Trưởng/Phó phòng">Trưởng/Phó phòng</option>
                            <option value="Quản lý">Quản lý / Giám sát</option>
                            <option value="Giám đốc">Giám đốc</option>
                            <option value="Phó giám đốc">Trưởng chi nhánh</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mức lương</label>
                        <select name="salary">
                            <option value="">Tất cả mức lương</option>
                            <option value="Dưới 3 triệu">Dưới 10 triệu</option>
                            <option value="10 - 12 triệu">10 - 15 triệu</option>
                            <option value="15 - 20 triệu">15 - 20 triệu</option>
                            <option value="20 - 25 triệu">20 - 25 triệu</option>
                            <option value="25 - 30 triệu">25 - 30 triệu</option>
                            <option value="30 - 40 triệu">30 - 50 triệu</option>
                            <option value="Trên 50 triệu">Trên 50 triệu</option>
                            <option value="Thoả thuận">Thoả thuận</option>
                        </select>
                    </div>

                    <div class="slider-container">
                        <label>Số lượng công việc muốn crawl</label>
                        <input type="range" name="search_range" min="5" max="50"
                            value="{{ old('search_range', 20) }}"
                            oninput="this.nextElementSibling.textContent = this.value + ' công việc'">
                        <div class="slider-value">{{ old('search_range', 20) }} công việc</div>
                    </div>

                    <button type="submit" class="btn btn-crawl">
                        <i class="fa-solid fa-circle-play"></i>
                        Bắt đầu Crawl Ngay
                    </button>
                </form>

                @if (session('success'))
                    <div class="status-message success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error') && request()->is('dashboard'))
                    <div class="status-message error">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Bên phải: Upload CV & Match -->
        <div class="panel panel-right">
            <h2 class="panel-title">
                <span class="animated-text">Tìm Việc</span><br> Phù Hợp Với CV
            </h2>
            <p class="panel-desc">AI sẽ phân tích CV và gợi ý những công việc phù hợp nhất từ dữ liệu mới nhất</p>

            <div class="form-wrapper">
                <form action="{{ route('match.cv') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Upload CV của bạn</label>
                        <input type="file" name="cv_file" accept=".pdf,.docx,.txt" required>
                    </div>

                    <div class="form-group">
                        <label>Kỹ năng bổ sung (tùy chọn)</label>
                        <input type="text" name="extra_skills" placeholder="ex: Docker, AWS, Laravel..."
                            value="{{ old('extra_skills') }}">
                    </div>

                    <div class="form-group">
                        <label>Vị trí mong muốn (tùy chọn)</label>
                        <input type="text" name="desired_position" placeholder="ex: Senior Frontend Developer"
                            value="{{ old('desired_position') }}">
                    </div>

                    <button type="submit" class="btn btn-match">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Tìm Việc Phù Hợp
                    </button>
                </form>

                @if (session('results'))
                    <div class="results">
                        <h3 style="text-align:center; margin:30px 0 20px; font-size:1.5rem; color:#00ffaa;">
                            Top 10 công việc phù hợp nhất
                        </h3>
                        @foreach (session('results') as $job)
                            <div class="job-card">
                                <div class="job-title">{{ $job['Vị trí'] }}</div>
                                <div class="score">{{ $job['Matching Score (%)'] }}%</div>
                                <div class="info">
                                    <strong>Lương:</strong> {{ $job['Mức lương'] }}<br>
                                    <strong>Kinh nghiệm:</strong> {{ $job['Kinh nghiệm'] }}<br>
                                    <strong>Địa điểm:</strong> {{ $job['Địa điểm'] }}
                                </div>
                                <div class="skills">
                                    <strong>Kỹ năng phù hợp:</strong> {{ $job['Kỹ năng phù hợp'] ?: 'Không xác định' }}
                                </div>
                                <a href="{{ $job['url'] }}" target="_blank" class="apply-btn">
                                    Ứng tuyển ngay →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (session('error') && session()->has('results') == false)
                    <div class="status-message error">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
