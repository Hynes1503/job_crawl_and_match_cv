<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Matcher AI')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('styles') <!-- ĐÃ SỬA: thêm dấu nháy đóng -->

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #000;
            color: #fff;
            position: relative;
            overflow-x: hidden;
            padding-top: 90px;
            /* Quan trọng: chừa chỗ cho navbar fixed */
        }

        /* Background gradient động */
        body::before {
            /* giữ nguyên như cũ */
        }

        @keyframes movingGradient {
            /* giữ nguyên */
        }

        body::after {
            /* giữ nguyên */
        }

        .main-content {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 90px);
            padding: 20px 20px 60px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 80px;
            }

            .main-content {
                padding: 16px 16px 60px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 70px;
            }
        }
    </style>
</head>

<body>

    @include('partials.navbar') <!-- ĐÃ SỬA: thêm dấu ngoặc và nháy -->

    <main class="main-content">
        @yield('content') <!-- ĐÃ SỬA: thêm dấu ngoặc -->
    </main>

    @include('partials.footer') <!-- ĐÃ SỬA: thêm dấu ngoặc và nháy -->

    @stack('scripts') <!-- ĐÃ SỬA: thêm dấu nháy đóng -->

</body>

</html>
