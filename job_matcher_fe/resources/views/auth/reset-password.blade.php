<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Job Matcher AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

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

        .min-vh-100 {
            min-height: 100vh;
        }

        .reset-panel {
            width: 100%;
            max-width: 480px;
            padding: 48px 40px;
            background: rgba(15, 15, 25, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        .panel-title {
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .panel-desc {
            opacity: 0.85;
            font-size: 1.1rem;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 1rem;
            opacity: 0.95;
        }

        .form-control {
            width: 100%;
            padding: 16px 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
        }

        .form-control:disabled {
            background: rgba(255, 255, 255, 0.08);
            opacity: 0.7;
            cursor: not-allowed;
        }


        .alert-danger {
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.4);
            color: #ff6b6b;
            padding: 16px 20px;
            border-radius: 14px;
            text-align: left;
            font-size: 0.95rem;
            margin-bottom: 28px;
        }

        .btn-reset {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000000;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 18px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(0, 180, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
        }

        .btn-reset:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 180, 255, 0.5);
        }

        .btn-reset:active {
            transform: translateY(-1px);
        }

        @media (max-width: 576px) {
            .reset-panel {
                padding: 40px 24px;
                margin: 20px;
                border-radius: 20px;
            }

            .panel-title {
                font-size: 2.1rem;
            }

            .form-control {
                padding: 14px 16px;
            }

            .btn-reset {
                padding: 16px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
        <div class="reset-panel">
            <div class="panel-header text-center mb-4">
                <h3 class="panel-title">
                    <span class="animated-text">Đặt Lại Mật Khẩu</span>
                </h3>
                <p class="panel-desc">Vui lòng nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach ($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="#">
                <input type="hidden" name="email" value="user@example.com">

                <div class="form-group mb-4">
                    <label class="form-label">Tài khoản</label>
                    <input type="text" class="form-control" value="user@example.com" disabled>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control" placeholder="Ít nhất 6 ký tự" required minlength="6">
                </div>

                <div class="form-group mb-5">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu mới" required>
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fas fa-key"></i> Đổi Mật Khẩu
                </button>
            </form>
        </div>
    </div>
</body>
</html>