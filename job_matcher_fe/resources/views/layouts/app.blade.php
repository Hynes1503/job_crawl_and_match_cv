<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Matcher AI')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('styles')

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background: #0a0a0f;
            color: #ffffff;
            position: relative;
            overflow-x: hidden;
            padding-top: 80px;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(0, 180, 255, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(0, 255, 170, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 100, 255, 0.08) 0%, transparent 60%);
            animation: movingGradient 20s ease infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes movingGradient {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }

            33% {
                transform: translate(-5%, 5%) scale(1.05);
                opacity: 0.9;
            }

            66% {
                transform: translate(5%, -5%) scale(0.95);
                opacity: 0.8;
            }
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='3.5' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
        }

        .main-content {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 80px);
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px 80px;
        }

        @media (max-width: 1200px) {
            .main-content {
                max-width: 100%;
                padding: 24px 20px 80px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .main-content {
                min-height: calc(100vh - 70px);
                padding: 20px 16px 80px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 65px;
            }

            .main-content {
                padding: 16px 12px 80px;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #00b4ff, #00ffaa);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00d4ff, #00ffcc);
        }

        ::selection {
            background: rgba(0, 180, 255, 0.3);
            color: #ffffff;
        }

        ::-moz-selection {
            background: rgba(0, 180, 255, 0.3);
            color: #ffffff;
        }

        .toast-container {
            position: fixed;
            top: 90px;
            right: 24px;
            z-index: 10000;
            max-width: 420px;
            pointer-events: auto;
        }

        .toast-notification {
            display: flex;
            align-items: center;
            background: rgba(20, 20, 35, 0.92);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 14px;
            border: 1px solid rgba(0, 180, 255, 0.18);
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast-icon {
            font-size: 1.6rem;
            margin-right: 14px;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            color: #00ffaa;
        }

        .toast-error .toast-icon {
            color: #ff5e5e;
        }

        .toast-warning .toast-icon {
            color: #ffcc00;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 0.95rem;
            opacity: 0.92;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 4px;
            margin-left: 12px;
            transition: all 0.2s;
        }

        .toast-close:hover {
            color: white;
            transform: rotate(90deg);
        }

        /* Animation khi đóng */
        .toast-notification.hiding {
            opacity: 0;
            transform: translateX(120%);
        }

        @media (max-width: 768px) {
            .toast-container {
                right: 16px;
                left: 16px;
                max-width: none;
            }
        }
    </style>
</head>

<body>

    @include('partials.navbar')
    <div class="toast-container" id="toastContainer">
        @if (session('success'))
            <div class="toast-notification toast-success" data-auto-close="true">
                <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                <div class="toast-content">
                    <div class="toast-title">Thành công!</div>
                    <div class="toast-message">{{ session('success') }}</div>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-notification toast-error" data-auto-close="true">
                <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="toast-content">
                    <div class="toast-title">Lỗi!</div>
                    <div class="toast-message">{{ session('error') }}</div>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast-notification toast-warning" data-auto-close="true">
                <div class="toast-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="toast-content">
                    <div class="toast-title">Cảnh báo</div>
                    <div class="toast-message">{{ session('warning') }}</div>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        @endif
    </div>
    <main class="main-content">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 30000);
                }
            });
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            window.axios = window.axios || {};
            window.axios.defaults = window.axios.defaults || {};
            window.axios.defaults.headers = window.axios.defaults.headers || {};
            window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
        }
    </script>

</body>

</html>
