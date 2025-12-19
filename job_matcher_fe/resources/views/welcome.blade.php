<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Job Matcher AI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: Inter, system-ui;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #000;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        /* ===== BACKGROUND CHUYỂN ĐỘNG MÀU ===== */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 180, 255, 0.4), transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(100, 255, 200, 0.3), transparent 45%),
                radial-gradient(circle at 50% 90%, rgba(255, 100, 200, 0.25), transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255, 200, 100, 0.2), transparent 45%);
            background-size: 200% 200%;
            animation: movingGradient 20s ease infinite;
            z-index: -2;
            opacity: 0.8;
        }

        @keyframes movingGradient {
            0% {
                background-position: 0% 0%, 100% 100%, 50% 50%, 0% 100%;
            }
            25% {
                background-position: 100% 0%, 0% 100%, 50% 0%, 100% 50%;
            }
            50% {
                background-position: 100% 100%, 0% 0%, 0% 50%, 50% 100%;
            }
            75% {
                background-position: 0% 100%, 100% 0%, 100% 100%, 0% 0%;
            }
            100% {
                background-position: 0% 0%, 100% 100%, 50% 50%, 0% 100%;
            }
        }

        /* Thêm một lớp blur nhẹ cho hiệu ứng sâu hơn */
        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background: inherit;
            filter: blur(120px);
            animation: movingGradient 20s ease infinite;
            z-index: -1;
            opacity: 0.6;
        }

        /* ===== LAYOUT ===== */
        .layout {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ===== LEFT VISUAL ===== */
        .visual {
            position: relative;
            padding: 80px;
            display: flex;
            align-items: center;
        }

        .visual::before {
            content: "";
            position: absolute;
            inset: -30%;
            background:
                radial-gradient(circle at 20% 30%, rgba(0, 180, 255, .35), transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, .25), transparent 45%);
            filter: blur(160px);
            animation: glow 14s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                transform: translate(-10%, -10%) scale(1);
            }

            to {
                transform: translate(10%, 10%) scale(1.15);
            }
        }

        .visual-content {
            position: relative;
            z-index: 1;
            max-width: 520px;
        }

        .visual h1 {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 24px;
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

        .visual p {
            opacity: .75;
            line-height: 1.7;
        }

        /* ===== RIGHT ===== */
        .right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .wrapper {
            width: 100%;
            max-width: 420px;
            padding: 44px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 22px;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(10px);
        }

        .logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 32px;
            border-radius: 50%;
            background: #fff;
            color: #000;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 999px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: .25s;
            margin-bottom: 14px;
        }

        .btn:last-child {
            margin-bottom: 0;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: .9;
        }

        .btn-primary {
            background: #fff;
            color: #000;
        }

        .btn-outline {
            background: transparent;
            color: #fff;
            border: 1px solid #333;
        }

        /* ===== SOCIAL BUTTON ===== */
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            white-space: nowrap;
        }

        .social-btn img {
            width: 18px;
            height: 18px;
        }

        /* ===== MODAL ===== */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .85);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .modal.active {
            display: flex;
        }

        .modal-box {
            width: 100%;
            max-width: 420px;
            padding: 36px 28px;
            background: rgba(0, 0, 0, .92);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, .15);
            backdrop-filter: blur(20px);
            animation: slideUp .3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-box h3 {
            text-align: center;
            margin-bottom: 24px;
        }

        .modal-box input {
            width: 100%;
            padding: 14px;
            background: #111;
            border: 1px solid #222;
            border-radius: 14px;
            color: #fff;
            margin-bottom: 14px;
        }

        .divider {
            margin: 18px 0;
            text-align: center;
            opacity: .6;
            font-size: .85rem;
        }

        .modal-switch {
            margin-top: 18px;
            text-align: center;
            font-size: .9rem;
            opacity: .75;
        }

        .modal-switch span {
            cursor: pointer;
            text-decoration: underline;
        }

        .close {
            margin-top: 18px;
            text-align: center;
            opacity: .6;
            cursor: pointer;
            font-size: .85rem;
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .visual {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="layout">
        <div class="visual">
            <div class="visual-content">
                <h1>
                    <span class="animated-text">Job Matcher</span><br>
                    <span class="animated-text">AI Platform</span>
                </h1>
                <p>Phân tích CV & gợi ý công việc bằng AI.</p>
            </div>
        </div>

        <div class="right">
            <div class="wrapper">
                <div class="logo">JM</div>
                <h2>Welcome</h2>
                <p>Đăng nhập hoặc tạo tài khoản mới</p>

                <button class="btn btn-primary" onclick="openLogin()">Đăng nhập</button>
                <button class="btn btn-outline" onclick="openRegister()">Đăng ký</button>
            </div>
        </div>
    </div>

    <!-- LOGIN MODAL -->
    <div id="loginModal" class="modal">
        <div class="modal-box">
            <h3>Đăng nhập</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <button class="btn btn-primary">Login</button>
            </form>

            <div class="divider">hoặc</div>

            <a href="{{ route('auth.google') }}" class="btn btn-outline social-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg">
                Đăng nhập với Google
            </a>

            <a href="{{ route('auth.github') }}" class="btn btn-outline social-btn">
                <img src="https://www.svgrepo.com/show/475654/github-color.svg">
                Đăng nhập với GitHub
            </a>

            <div class="modal-switch">
                <span onclick="switchToRegister()">Chưa có tài khoản? Đăng ký</span>
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
                <input type="text" name="name" placeholder="Tên hiển thị" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                <button class="btn btn-primary">Register</button>
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