@extends('layouts.app')

@section('title', 'Dashboard - Job Matcher AI')

@push('styles')
    <style>
        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .crawl-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .crawl-card {
            width: 100%;
            max-width: 700px;
            background: rgba(15, 15, 25, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 32px;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease;
        }

        .crawl-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 180, 255, 0.15), transparent);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .card-content {
            position: relative;
            z-index: 1;
        }
        .crawl-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .crawl-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.2), rgba(0, 255, 170, 0.2));
            border: 2px solid rgba(0, 180, 255, 0.4);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.5rem;
        }

        .crawl-title {
            font-size: 2.8rem;
            font-weight: 900;
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .crawl-subtitle {
            font-size: 1.1rem;
            opacity: 0.8;
            color: #bbd0ff;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
        }

        .form-label i {
            color: #00b4ff;
            font-size: 1rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(0, 0, 0, 0.5);
        }

        .form-input::placeholder {
            color: #888;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2300b4ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 44px;
            cursor: pointer;
        }

        .form-select option {
            background: #1a1a2e;
            color: #fff;
            padding: 10px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .slider-group {
            margin: 32px 0;
        }

        .slider-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .slider-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #fff;
        }

        .slider-label i {
            color: #00b4ff;
        }

        .slider-value {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(120deg, #00b4ff, #00ffaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(0, 255, 150, 0.3);
        }

        .range-slider {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            outline: none;
            appearance: none;
            cursor: pointer;
            position: relative;
        }

        .range-slider::-webkit-slider-thumb {
            appearance: none;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 20px rgba(0, 180, 255, 0.6);
            transition: all 0.3s ease;
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            box-shadow: 0 0 30px rgba(0, 180, 255, 0.8);
        }

        .range-slider::-moz-range-thumb {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 0 20px rgba(0, 180, 255, 0.6);
        }
        .slider-track {
            height: 4px;
            background: rgba(0, 180, 255, 0.3);
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .slider-fill {
            height: 100%;
            background: linear-gradient(90deg, #00b4ff, #00ffaa);
            transition: width 0.2s ease;
            border-radius: 4px;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            padding: 18px;
            border: none;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.15rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 32px;
            box-shadow: 0 8px 24px rgba(0, 180, 255, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 180, 255, 0.5);
        }

        .submit-btn:active {
            transform: translateY(-2px);
        }

        .submit-btn i {
            font-size: 1.5rem;
        }

        .status-alert {
            margin-top: 24px;
            padding: 18px 24px;
            border-radius: 14px;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            animation: fadeIn 0.5s ease;
        }

        .status-alert i {
            font-size: 1.3rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(0, 255, 150, 0.15), rgba(0, 255, 150, 0.08));
            border: 1px solid rgba(0, 255, 150, 0.4);
            color: #00ffaa;
        }

        .alert-error {
            background: linear-gradient(135deg, rgba(255, 100, 100, 0.15), rgba(255, 100, 100, 0.08));
            border: 1px solid rgba(255, 100, 100, 0.4);
            color: #ff6b6b;
        }

        .info-box {
            background: rgba(0, 180, 255, 0.08);
            border: 1px solid rgba(0, 180, 255, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-box i {
            color: #00b4ff;
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .info-box p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .filters-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .crawl-container {
                padding: 20px 16px;
            }

            .crawl-card {
                padding: 32px 24px;
                border-radius: 24px;
            }

            .crawl-title {
                font-size: 2.2rem;
            }

            .crawl-subtitle {
                font-size: 1rem;
            }

            .filters-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .slider-value {
                font-size: 1.5rem;
            }

            .submit-btn {
                padding: 16px;
                font-size: 1.05rem;
            }
        }

        @media (max-width: 480px) {
            .crawl-card {
                padding: 24px 20px;
            }

            .crawl-icon {
                width: 70px;
                height: 70px;
                font-size: 2rem;
            }

            .crawl-title {
                font-size: 2rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="crawl-container">
        <div class="crawl-card">
            <div class="card-content">
                <div class="crawl-header">
                    <h1 class="crawl-title">Crawl Công Việc IT</h1>
                    <p class="crawl-subtitle">
                        Thu thập dữ liệu việc làm mới nhất từ TopCV với các tiêu chí tùy chỉnh
                    </p>
                </div>

                <form action="{{ route('crawl.jobs') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="keyword" class="form-label">
                            <i class="fas fa-search"></i>
                            Từ khóa (tùy chọn)
                        </label>
                        <input type="text" id="keyword" name="keyword" class="form-input"
                            placeholder="VD: PHP, React, Java, Python..."
                            value="{{ old('keyword') }}">
                    </div>

                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Địa điểm
                            </label>
                            <select id="location" name="location" class="form-select">
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
                            <label for="level" class="form-label">
                                <i class="fas fa-layer-group"></i>
                                Cấp bậc
                            </label>
                            <select id="level" name="level" class="form-select">
                                <option value="">Tất cả cấp bậc</option>
                                <option value="Thực tập sinh">Thực tập sinh</option>
                                <option value="Nhân viên">Nhân viên</option>
                                <option value="Trưởng nhóm">Trưởng nhóm</option>
                                <option value="Trưởng/Phó phòng">Trưởng/Phó phòng</option>
                                <option value="Quản lý">Quản lý / Giám sát</option>
                                <option value="Giám đốc">Giám đốc</option>
                                <option value="Trưởng chi nhánh">Trưởng chi nhánh</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="salary" class="form-label">
                                <i class="fas fa-dollar-sign"></i>
                                Mức lương
                            </label>
                            <select id="salary" name="salary" class="form-select">
                                <option value="">Tất cả mức lương</option>
                                <option value="Dưới 10 triệu">Dưới 10 triệu</option>
                                <option value="10 - 15 triệu">10 - 15 triệu</option>
                                <option value="15 - 20 triệu">15 - 20 triệu</option>
                                <option value="20 - 25 triệu">20 - 25 triệu</option>
                                <option value="25 - 30 triệu">25 - 30 triệu</option>
                                <option value="30 - 50 triệu">30 - 50 triệu</option>
                                <option value="Trên 50 triệu">Trên 50 triệu</option>
                                <option value="Thoả thuận">Thoả thuận</option>
                            </select>
                        </div>
                    </div>

                    <div class="slider-group">
                        <div class="slider-header">
                            <span class="slider-label">
                                <i class="fas fa-sliders-h"></i>
                                Số lượng công việc
                            </span>
                            <span class="slider-value" id="sliderValue">{{ old('search_range', 20) }}</span>
                        </div>
                        <input type="range" name="search_range" min="1" max="50"
                            value="{{ old('search_range', 20) }}"
                            class="range-slider"
                            oninput="updateSliderValue(this.value)">
                        <div class="slider-track">
                            <div class="slider-fill" id="sliderFill" style="width: {{ (old('search_range', 20) / 50) * 100 }}%"></div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-rocket"></i>
                        Bắt Đầu Crawl Ngay
                    </button>
                </form>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>Dữ liệu sẽ được crawl từ TopCV. Thời gian xử lý phụ thuộc vào số lượng công việc.</p>
                </div>

                @if (session('success'))
                    <div class="status-alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error') && request()->is('dashboard'))
                    <div class="status-alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSliderValue(value) {
            document.getElementById('sliderValue').textContent = value;
            const percentage = (value / 50) * 100;
            document.getElementById('sliderFill').style.width = percentage + '%';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('.range-slider');
            updateSliderValue(slider.value);
        });
    </script>
@endpush