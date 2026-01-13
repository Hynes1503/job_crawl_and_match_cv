<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - Job Matcher')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    <style>
        body {
            background-color: #0f0f1a;
            color: #e0e0ff;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #1a1a2e, #16213e);
            border-right: 1px solid #2a2a40;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(-250px);
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background-color: #0f0f1a;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Header với toggle và tiêu đề trang */
        .admin-header {
            background: #1a1a2e;
            border-bottom: 1px solid #2a2a40;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
        }

        .toggle-btn {
            background: #00b4ff;
            color: #000;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(0, 180, 255, 0.4);
            transition: all 0.3s ease;
        }

        .toggle-btn:hover {
            background: #00d4ff;
            transform: translateY(-2px);
        }

        .toggle-btn i {
            transition: transform 0.3s ease;
        }

        .toggle-btn.active i {
            transform: rotate(90deg);
        }

        .nav-link.text-white {
            color: #bbd0ff !important;
            padding: 14px 20px;
            border-radius: 10px;
            margin: 4px 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
        }

        .nav-link.text-white i {
            width: 24px;
            text-align: center;
        }

        .nav-link.text-white:hover,
        .nav-link.text-white.active {
            background: rgba(0, 180, 255, 0.25);
            color: #00d4ff !important;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0, 180, 255, 0.2);
        }

        .nav-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff4444;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
            border-radius: 50%;
            padding: 0 4px;
            text-align: center;
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .navbar-brand.text-white {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .alert-success {
            background-color: rgba(0, 255, 150, 0.15);
            border: 1px solid #00ffaa;
            color: #00ffaa;
            border-radius: 12px;
        }

        .dropdown-menu {
            background-color: #1a1a2e;
            border: 1px solid #2a2a40;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .dropdown-item {
            color: #bbd0ff;
            padding: 10px 16px;
        }

        .dropdown-item:hover {
            background-color: rgba(0, 180, 255, 0.2);
            color: #00d4ff;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .main-content {
                margin-left: 0;
            }

            .admin-header {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="sidebar" id="sidebar">
        <div class="p-4 text-center border-bottom border-2" style="border-color: #2a2a40 !important;">
            <a class="navbar-brand text-white d-inline-block" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-cogs fa-lg me-2"></i> Admin Panel
            </a>
        </div>

        <ul class="nav flex-column px-3 py-3">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <li class="nav-item position-relative">
                <a class="nav-link text-white {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}"
                    href="{{ route('admin.tickets.index') }}">
                    <i class="fa-solid fa-ticket"></i> Quản Lý Ticket
                    @if($newTicketsCount > 0)
                        <span class="nav-badge">{{ $newTicketsCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item position-relative">
                <a class="nav-link text-white {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                    href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i> Quản Lý Người Dùng
                </a>
            </li>

            <li class="nav-item position-relative">
                <a class="nav-link text-white {{ request()->routeIs('admin.logs*') ? 'active' : '' }}"
                    href="{{ route('admin.logs.index') }}">
                    <i class="fas fa-history"></i> Nhật Ký Người Dùng
                    @if($newLogsCount > 0)
                        <span class="nav-badge">{{ $newLogsCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item position-relative">
                <a class="nav-link text-white {{ request()->routeIs('admin.deleted.crawls*') ? 'active' : '' }}"
                    href="{{ route('admin.deleted.crawls') }}">
                    <i class="fas fa-trash-restore"></i> Crawl Đã Xóa
                    @if($newDeletedCrawlsCount > 0)
                        <span class="nav-badge">{{ $newDeletedCrawlsCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('admin.site-selectors*') ? 'active' : '' }}"
                    href="{{ route('admin.site-selectors.index') }}">
                    <i class="fas fa-code"></i> Site Selectors
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('admin.statistics*') ? 'active' : '' }}"
                    href="{{ route('admin.statistics') }}">
                    <i class="fa-solid fa-chart-pie"></i> Thống kê
                </a>
            </li>

            <li class="nav-item mt-auto mb-4">
                <div class="dropdown">
                    <a class="nav-link text-white dropdown-toggle d-flex align-items-center px-3" href="#"
                        id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg"></i>
                        <div class="ms-3 text-start">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small style="opacity: 0.7;">{{ ucfirst(auth()->user()->role) }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-2"></i> Trang Người Dùng
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Đăng Xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
    <div class="main-content" id="mainContent">
        <header class="admin-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="toggle-btn me-4" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="h4 mb-0 text-white fw-semibold">@yield('page-title', 'Dashboard')</h1>
            </div>
        </header>

        <div class="container-fluid py-4 px-4 px-md-5">
            @if (session('success'))
                <div class="toast-notification success" id="toastNotification">
                    <div class="toast-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">Thành công!</div>
                        <div class="toast-message">{{ session('success') }}</div>
                    </div>
                    <button class="toast-close" onclick="closeToast()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="toast-notification error" id="toastNotification">
                    <div class="toast-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">Lỗi!</div>
                        <div class="toast-message">{{ session('error') }}</div>
                    </div>
                    <button class="toast-close" onclick="closeToast()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if (session('warning'))
                <div class="toast-notification warning" id="toastNotification">
                    <div class="toast-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">Cảnh báo!</div>
                        <div class="toast-message">{{ session('warning') }}</div>
                    </div>
                    <button class="toast-close" onclick="closeToast()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('active');

            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.replace('fa-times', 'fa-bars');
            } else {
                toggleIcon.classList.replace('fa-bars', 'fa-times');
            }
        });

        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    toggleBtn.classList.remove('active');
                    toggleIcon.classList.replace('fa-times', 'fa-bars');
                }
            });
        });

        function closeToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                setTimeout(() => closeToast(), 4000);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>