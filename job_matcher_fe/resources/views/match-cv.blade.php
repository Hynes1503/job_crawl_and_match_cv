@extends('layouts.app')

@section('title', 'Dashboard - Job Matcher AI')

@push('styles')
    <style>
        /* Container chính - tối ưu cho màn hình 16:9, không tràn */
        .content-container {
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(14px);
            min-height: 100vh;
            padding: 40px 20px 60px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }

        /* Panel crawl gọn gàng, vừa màn hình 16:9 */
        .crawl-panel {
            width: 100%;
            max-width: 620px; /* Giảm từ 760px → gọn hơn */
            padding: 0 20px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            margin-top: 20px;
        }

        /* Tiêu đề nhỏ hơn, vừa màn hình */
        .panel-title {
            font-size: 2.2rem; /* Giảm từ 2.6rem */
            font-weight: 800;
            text-align: center;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Mô tả ngắn gọn hơn */
        .panel-desc {
            opacity: 0.85;
            text-align: center;
            max-width: 520px;
            margin-bottom: 32px;
            line-height: 1.6;
            font-size: 1rem;
        }

        /* Form wrapper nhỏ hơn */
        .form-wrapper {
            width: 100%;
            max-width: 600px; /* Giảm từ 720px */
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            opacity: 0.95;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 16px; /* Giảm padding */
            background: rgba(17, 17, 17, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23bbbbbb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
            padding-right: 44px;
        }

        /* 3 select ngang nhưng nhỏ hơn */
        .filters-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filters-row .form-group {
            flex: 1;
            min-width: 170px; /* Giảm min-width */
            margin-bottom: 0;
        }

        /* Slider nhỏ gọn */
        .slider-container {
            margin: 28px 0 24px;
            width: 100%;
        }

        .slider-container label {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        input[type="range"] {
            width: 100%;
            height: 7px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 7px;
            outline: none;
            appearance: none;
            cursor: pointer;
        }

        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(0, 180, 255, 0.5);
            transition: all 0.3s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .slider-value {
            text-align: center;
            font-size: 1.4rem; /* Giảm từ 1.6rem */
            font-weight: 800;
            color: #00ffaa;
            margin-top: 10px;
            text-shadow: 0 0 12px rgba(0, 255, 150, 0.3);
        }

        /* Nút crawl nhỏ hơn, vừa phải */
        .btn {
            width: 100%;
            padding: 16px; /* Giảm từ 18px */
            border-radius: 999px;
            font-size: 1.1rem; /* Giảm từ 1.2rem */
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.4s ease;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn i {
            font-size: 1.4rem;
        }

        .btn-crawl {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000000;
        }

        .btn-crawl:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 180, 255, 0.4);
        }

        /* Thông báo nhỏ gọn */
        .status-message {
            padding: 16px 20px;
            border-radius: 14px;
            text-align: center;
            font-weight: 600;
            max-width: 600px;
            margin: 24px auto 0;
            font-size: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .success {
            background: rgba(0, 255, 150, 0.15);
            border: 1px solid rgba(0, 255, 150, 0.4);
            color: #00ffaa;
        }

        .error {
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.4);
            color: #ff6b6b;
        }

        /* Responsive - tối ưu cho 16:9 */
        @media (max-width: 992px) {
            .crawl-panel {
                max-width: 560px;
            }
            .panel-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 20px 16px 60px;
            }

            .crawl-panel {
                margin-top: 10px;
                padding: 0 10px 30px;
            }

            .panel-title {
                font-size: 1.8rem;
            }

            .panel-desc {
                font-size: 0.95rem;
                margin-bottom: 28px;
            }

            .filters-row {
                flex-direction: column;
                gap: 16px;
            }

            .slider-value {
                font-size: 1.3rem;
            }

            .btn {
                padding: 15px;
                font-size: 1.05rem;
            }
        }

        @media (max-width: 480px) {
            .panel-title {
                font-size: 1.7rem;
            }

            .form-wrapper {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="crawl-panel">
            <h2 class="panel-title">
                <span class="animated-text">Crawl</span><br>
                <span class="animated-text">Công Việc IT</span>
            </h2>
            <p class="panel-desc">
                Thu thập dữ liệu việc làm mới nhất từ TopCV với các tiêu chí tùy chỉnh.
            </p>

            <div class="form-wrapper">
                <form action="{{ route('crawl.jobs') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="keyword">Từ khóa (tùy chọn)</label>
                        <input type="text" id="keyword" name="keyword" placeholder="Ví dụ: PHP, React, Java..." value="{{ old('keyword') }}">
                    </div>

                    <div class="filters-row">
                        <div class="form-group">
                            <label for="location">Tỉnh / Thành phố</label>
                            <select id="location" name="location">
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
                            <label for="level">Cấp bậc</label>
                            <select id="level" name="level">
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
                            <label for="salary">Mức lương</label>
                            <select id="salary" name="salary">
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

                    <div class="slider-container">
                        <label>Số lượng công việc muốn crawl</label>
                        <input type="range" name="search_range" min="1" max="50"
                            value="{{ old('search_range', 20) }}"
                            oninput="updateSliderValue(this.value)">
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
    </div>
@endsection

@push('scripts')
    <script>
        function updateSliderValue(value) {
            document.querySelector('.slider-value').textContent = value + ' công việc';
        }
    </script>
@endpush