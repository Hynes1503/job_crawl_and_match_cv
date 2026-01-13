<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Matcher AI - Đăng nhập / Đăng ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a0f2e 100%);
            color: #e0e0ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .auth-container {
            width: 420px;
            max-width: 95vw;
            background: rgba(15, 15, 25, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(0, 180, 255, 0.15), transparent);
            animation: rotate 20s linear infinite;
            pointer-events: none;
            opacity: 0.4;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo h1 {
            font-size: 2.4rem;
            font-weight: 800;
            background: linear-gradient(120deg, #00b4ff, #00ffaa, #00b4ff);
            background-size: 200% 200%;
            animation: textGradient 6s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo p {
            opacity: 0.7;
            font-size: 1rem;
            margin-top: 8px;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h3 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 28px;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 180, 255, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #bbd0ff;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #00b4ff;
            color: #ffffff;
        }

        .social-btn img {
            width: 20px;
            height: 20px;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            opacity: 0.6;
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
            background: rgba(15, 15, 25, 0.8);
            padding: 0 16px;
        }

        .modal-switch {
            text-align: center;
            margin-top: 24px;
            font-size: 0.95rem;
        }

        .modal-switch span {
            color: #00b4ff;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-switch span:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 32px 24px;
                border-radius: 20px;
            }
            .logo h1 { font-size: 2rem; }
            h3 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

    <div id="loginModal" class="modal active">
        <div class="auth-container">
            <div class="logo">
                <h1>Job Matcher AI</h1>
                <p>Đăng nhập để tiếp tục</p>
            </div>

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
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
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
        </div>
    </div>

    <div id="registerModal" class="modal">
        <div class="auth-container">
            <div class="logo">
                <h1>Job Matcher AI</h1>
                <p>Tạo tài khoản mới</p>
            </div>

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
        </div>
    </div>

    <script>
        function switchToRegister() {
            document.getElementById('loginModal').classList.remove('active');
            document.getElementById('registerModal').classList.add('active');
        }

        function switchToLogin() {
            document.getElementById('registerModal').classList.remove('active');
            document.getElementById('loginModal').classList.add('active');
        }
    </script>
</body>
</html>