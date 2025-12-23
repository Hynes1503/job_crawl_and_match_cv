@extends('layouts.app')

@section('title', 'Dashboard - Job Matcher AI')

@push('styles')
    <style>
        /* Chỉ áp dụng cho content-container, KHÔNG đụng đến body */
        .content-container {
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(12px);
            min-height: calc(100vh - 90px); /* Đồng bộ với padding-top của body */
            padding: 20px 20px 60px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            border-radius: 16px;
            width: 100%;
            margin: 0 auto;
        }

        /* Panel chính */
        .crawl-panel {
            width: 100%;
            max-width: 700px;
            padding: 0px 25px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            margin-top: 20px; /* Khoảng cách nhẹ từ đỉnh để đẹp hơn */
        }

        .panel-title {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 6px;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
            background-size: 200% 200%;
            animation: textGradient 6s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .panel-desc {
            opacity: 0.85;
            text-align: center;
            max-width: 580px;
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 1rem;
        }

        .form-wrapper {
            width: 100%;
            max-width: 680px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 11px 14px;
            background: rgba(17, 17, 17, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
        }

        /* 3 select box ngang */
        .filters-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .filters-row .form-group {
            flex: 1;
            min-width: 190px;
            margin-bottom: 0;
        }

        /* Slider */
        .slider-container {
            margin: 20px 0;
        }

        input[type="range"] {
            width: 100%;
            height: 7px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 7px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            width: 22px;
            height: 22px;
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
            margin-top: 8px;
        }

        .btn {
            width: 100%;
            padding: 13px;
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

        .status-message {
            padding: 14px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            max-width: 680px;
            margin: 20px auto;
            font-size: 0.95rem;
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

        /* Responsive */
        @media (max-width: 768px) {
            .content-container {
                padding: 16px 16px 60px;
                border-radius: 12px;
            }

            .crawl-panel {
                padding: 15px 15px 25px;
                margin-top: 10px;
            }

            .panel-title {
                font-size: 1.8rem;
            }

            .filters-row {
                flex-direction: column;
            }

            .filters-row .form-group {
                min-width: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="crawl-panel">
            <h2 class="panel-title">
                <span class="animated-text">Crawl</span><br> Công Việc IT
            </h2>
            <p class="panel-desc">Thu thập dữ liệu việc làm mới nhất từ TopCV với các tiêu chí tùy chỉnh</p>

            <div class="form-wrapper">
                <form action="{{ route('crawl.jobs') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Từ khóa (tùy chọn)</label>
                        <input type="text" name="keyword" placeholder="ex: PHP, React, Java..." value="{{ old('keyword') }}">
                    </div>

                    <div class="filters-row">
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
                                <option value="Trưởng nhóm">Trưởng nhóm</option>
                                <option value="Trưởng/Phó phòng">Trưởng/Phó phòng</option>
                                <option value="Quản lý">Quản lý / Giám sát</option>
                                <option value="Giám đốc">Giám đốc</option>
                                <option value="Trưởng chi nhánh">Trưởng chi nhánh</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Mức lương</label>
                            <select name="salary">
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
    </div>
@endsection