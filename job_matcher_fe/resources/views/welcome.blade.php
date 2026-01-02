<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Matcher AI - Đăng nhập / Đăng ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0f1a;
            color: #e0e0ff;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Background gradient di chuyển nhẹ nhàng */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 180, 255, 0.25), transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 255, 150, 0.2), transparent 50%),
                radial-gradient(circle at 50% 10%, rgba(100, 200, 255, 0.15), transparent 50%);
            background-size: 150% 150%;
            animation: slowMove 30s ease infinite;
            z-index: -2;
            opacity: 0.8;
        }

        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background: inherit;
            filter: blur(100px);
            animation: slowMove 30s ease infinite;
            z-index: -1;
            opacity: 0.6;
        }

        @keyframes slowMove {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Layout chính */
        .layout {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left - Visual */
        .visual {
            padding: 80px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .visual-content {
            max-width: 560px;
        }

        .visual h1 {
            font-size: 3.6rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .visual p {
            font-size: 1.3rem;
            opacity: 0.8;
            line-height: 1.7;
        }

        /* Right - Form */
        .right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .wrapper {
            width: 100%;
            max-width: 440px;
            padding: 48px 36px;
            text-align: center;
            background: rgba(15, 15, 25, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            font-weight: 800;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper h2 {
            font-size: 2rem;
            margin-bottom: 12px;
        }

        .wrapper > p {
            opacity: 0.8;
            margin-bottom: 32px;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 180, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 180, 255, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #bbd0ff;
            backdrop-filter: blur(8px);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #00b4ff;
            color: #ffffff;
        }

        .social-btn img {
            width: 22px;
            height: 22px;
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            backdrop-filter: blur(8px);
        }

        .modal.active {
            display: flex;
        }

        .modal-box {
            width: 100%;
            max-width: 440px;
            padding: 48px 36px;
            background: rgba(15, 15, 25, 0.85);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            animation: modalSlide 0.4s ease;
        }

        @keyframes modalSlide {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-box h3 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 32px;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .modal-box input {
            width: 100%;
            padding: 16px 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modal-box input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .modal-box input:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
        }

        .divider {
            text-align: center;
            margin: 28px 0;
            opacity: 0.7;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0; right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            background: rgba(15, 15, 25, 0.85);
            padding: 0 20px;
        }

        .modal-switch {
            text-align: center;
            margin-top: 28px;
            font-size: 1rem;
        }

        .modal-switch span {
            color: #00b4ff;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-switch span:hover {
            text-decoration: underline;
        }

        .close {
            text-align: center;
            margin-top: 24px;
            opacity: 0.6;
            cursor: pointer;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .layout {
                grid-template-columns: 1fr;
            }
            .visual {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .wrapper, .modal-box {
                padding: 40px 24px;
                margin: 20px;
            }
            .visual h1 { font-size: 3rem; }
        }
    </style>
</head>
<body>

    <div class="layout">
        <!-- Left Visual -->
        <div class="visual">
            <div class="visual-content">
                <h1>
                    <span class="animated-text">Job Matcher</span><br>
                    <span class="animated-text">AI Platform</span>
                </h1>
                <p>Phân tích CV thông minh và gợi ý công việc phù hợp nhất bằng công nghệ AI hiện đại.</p>
            </div>
        </div>

        <!-- Right - Welcome Buttons -->
        <div class="right">
            <div class="wrapper">
                <div class="logo">JM</div>
                <h2>Chào mừng bạn!</h2>
                <p>Đăng nhập hoặc tạo tài khoản để bắt đầu</p>

                <button class="btn btn-primary" onclick="openLogin()">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
                <button class="btn btn-outline" onclick="openRegister()">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </div>
        </div>
    </div>

    <!-- LOGIN MODAL -->
    <div id="loginModal" class="modal">
        <div class="modal-box">
            <h3>Đăng nhập</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Đăng nhập
                </button>
            </form>

            <div class="divider"><span>hoặc</span></div>

            <a href="{{ route('auth.google') }}" class="btn btn-outline social-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                Đăng nhập với Google
            </a>

            <a href="{{ route('auth.github') }}" class="btn btn-outline social-btn">
                <img src="https://www.svgrepo.com/show/475654/github-color.svg" alt="GitHub">
                Đăng nhập với GitHub
            </a>

            <div class="modal-switch">
                <span onclick="switchToRegister()">Chưa có tài khoản? Đăng ký ngay</span>
            </div>

            <div class="close" onclick="closeLogin()">Đóng</div>
        </div>
    </div>

    <!-- REGISTER MODAL -->
    <div id="registerModal" class="modal">
        <div class="modal-box">
            <h3>Đăng ký</h3>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" placeholder="Tên hiển thị" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </form>

            <div class="modal-switch">
                <span onclick="switchToLogin()">Đã có tài khoản? Đăng nhập</span>
            </div>

            <div class="close" onclick="closeRegister()">Đóng</div>
        </div>
    </div>

    <script>
        function openLogin() {
            document.getElementById('loginModal').classList.add('active');
        }

        function closeLogin() {
            document.getElementById('loginModal').classList.remove('active');
        }

        function openRegister() {
            document.getElementById('registerModal').classList.add('active');
        }

        function closeRegister() {
            document.getElementById('registerModal').classList.remove('active');
        }

        function switchToRegister() {
            closeLogin();
            openRegister();
        }

        function switchToLogin() {
            closeRegister();
            openLogin();
        }
    </script>

</body>
</html>