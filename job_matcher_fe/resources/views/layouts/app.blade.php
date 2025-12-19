<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Matcher AI')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @stack('styles')  <!-- ĐÃ SỬA: thêm dấu nháy đóng -->

    <style>
        * { box-sizing: border-box; font-family: Inter, system-ui; }
        body { margin: 0; min-height: 100vh; background: #000; color: #fff; position: relative; overflow-x: hidden; }

        body::before {
            content: ""; position: absolute; inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 180, 255, 0.4), transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(100, 255, 200, 0.3), transparent 45%),
                radial-gradient(circle at 50% 90%, rgba(255, 100, 200, 0.25), transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255, 200, 100, 0.2), transparent 45%);
            background-size: 200% 200%; animation: movingGradient 20s ease infinite; z-index: -2; opacity: 0.8;
        }

        @keyframes movingGradient {
            0% {background-position: 0% 0%, 100% 100%, 50% 50%, 0% 100%;}
            25% {background-position: 100% 0%, 0% 100%, 50% 0%, 100% 50%;}
            50% {background-position: 100% 100%, 0% 0%, 0% 50%, 50% 100%;}
            75% {background-position: 0% 100%, 100% 0%, 100% 100%, 0% 0%;}
            100% {background-position: 0% 0%, 100% 100%, 50% 50%, 0% 100%;}
        }

        body::after {
            content: ""; position: absolute; inset: 0; background: inherit;
            filter: blur(120px); animation: movingGradient 20s ease infinite; z-index: -1; opacity: 0.6;
        }

        .main-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 120px 20px 60px; /* Tăng padding-top để chừa chỗ cho navbar fixed */
        }

        @media (max-width: 768px) {
            .main-content { padding: 110px 16px 60px; }
        }
    </style>
</head>
<body>

    @include('partials.navbar')     <!-- ĐÃ SỬA: thêm dấu ngoặc và nháy -->

    <main class="main-content">
        @yield('content')           <!-- ĐÃ SỬA: thêm dấu ngoặc -->
    </main>

    @include('partials.footer')      <!-- ĐÃ SỬA: thêm dấu ngoặc và nháy -->

    @stack('scripts')               <!-- ĐÃ SỬA: thêm dấu nháy đóng -->

</body>
</html>