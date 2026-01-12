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

        /* Animated Background */
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
            0%, 100% {
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

        /* Noise Overlay */
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

        /* Main Content */
        .main-content {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 80px);
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px 80px;
        }

        /* Responsive */
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

        /* Scrollbar Styling */
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

        /* Selection */
        ::selection {
            background: rgba(0, 180, 255, 0.3);
            color: #ffffff;
        }

        ::-moz-selection {
            background: rgba(0, 180, 255, 0.3);
            color: #ffffff;
        }
    </style>
</head>

<body>

    @include('partials.navbar')

    <main class="main-content">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')

    <!-- Global Scripts -->
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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

        // Add loading state to forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    
                    // Re-enable after 30 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 30000);
                }
            });
        });

        // CSRF token for AJAX requests
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