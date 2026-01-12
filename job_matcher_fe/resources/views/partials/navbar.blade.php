<style>
    @keyframes textGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Navbar */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: rgba(15, 15, 25, 0.85);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 180, 255, 0.2);
        z-index: 9999;
        animation: slideDown 0.6s ease;
    }

    .navbar-container {
        max-width: 1400px;
        margin: 0 auto;
        height: 100%;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Logo */
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .navbar-brand:hover {
        transform: translateY(-2px);
    }

    .brand-icon {
        width: 48px;
        height: 48px;
        background: white;
        border: 2px solid rgba(0, 180, 255, 0.4);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .navbar-brand:hover .brand-icon {
        transform: rotate(-5deg);
    }

    .brand-text {
        font-size: 1.5rem;
        font-weight: 900;
        background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
        background-size: 200% 200%;
        animation: textGradient 8s ease infinite;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }

    /* Navigation Menu */
    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link i {
        font-size: 1.1rem;
        opacity: 0.8;
    }

    .nav-link:hover {
        color: #00b4ff;
        background: rgba(0, 180, 255, 0.1);
    }

    .nav-link.active {
        color: #00b4ff;
        background: rgba(0, 180, 255, 0.15);
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background: linear-gradient(90deg, #00b4ff, #00ffaa);
        border-radius: 3px 3px 0 0;
    }

    /* User Dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-toggle {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 16px;
        background: rgba(0, 180, 255, 0.1);
        border: 1px solid rgba(0, 180, 255, 0.3);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .user-toggle:hover {
        background: rgba(0, 180, 255, 0.2);
        border-color: rgba(0, 180, 255, 0.5);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #00b4ff, #00ffaa);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #000;
        font-size: 0.9rem;
    }

    .user-name {
        font-weight: 600;
        color: #fff;
        font-size: 0.95rem;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: rgba(15, 15, 25, 0.98);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 180, 255, 0.25);
        border-radius: 12px;
        min-width: 220px;
        margin-top: 12px;
        padding: 12px 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.25s ease;
        z-index: 1000;
    }

    .user-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: rgba(0, 180, 255, 0.15);
        color: #00b4ff;
    }

    .dropdown-item.admin {
        color: #ffca28;
    }

    .dropdown-item.admin:hover {
        background: rgba(255, 202, 40, 0.15);
        color: #ffdb5c;
    }

    .dropdown-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.08);
        margin: 8px 0;
    }

    .logout-btn {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        color: #ff6b6b;
        cursor: pointer;
    }

    /* Mobile Toggle */
    .mobile-toggle {
        display: none;
        background: rgba(0, 180, 255, 0.15);
        border: 1px solid rgba(0, 180, 255, 0.3);
        color: #00b4ff;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.3rem;
        transition: all 0.3s ease;
    }

    .mobile-toggle:hover {
        background: rgba(0, 180, 255, 0.25);
        border-color: rgba(0, 180, 255, 0.5);
    }

    /* Mobile Menu */
    @media (max-width: 992px) {
        .navbar {
            height: 70px;
        }

        .navbar-container {
            padding: 0 20px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            font-size: 1.3rem;
        }

        .brand-text {
            font-size: 1.3rem;
        }

        .mobile-toggle {
            display: flex;
        }

        .navbar-menu {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            flex-direction: column;
            background: rgba(15, 15, 25, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 180, 255, 0.2);
            padding: 20px;
            gap: 12px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        .navbar-menu.active {
            max-height: 500px;
        }

        .nav-link {
            width: 100%;
            padding: 14px 20px;
            justify-content: flex-start;
        }

        /* Mobile User Dropdown */
        .user-dropdown {
            width: 100%;
        }

        .user-toggle {
            width: 100%;
            justify-content: center;
        }

        .dropdown-menu {
            position: static;
            width: 100%;
            margin-top: 12px;
            box-shadow: none;
            border: 1px solid rgba(0, 180, 255, 0.2);
        }
    }

    @media (max-width: 576px) {
        .navbar {
            height: 65px;
        }

        .navbar-container {
            padding: 0 16px;
        }

        .navbar-menu {
            top: 65px;
            padding: 16px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            font-size: 1.2rem;
        }

        .brand-text {
            font-size: 1.2rem;
        }
    }
</style>

<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="brand-icon"><i class="fa-solid fa-rocket"></i></div>
            <span class="brand-text">Job Matcher AI</span>
        </a>

        <!-- Desktop & Mobile Menu -->
        <ul class="navbar-menu" id="navbarMenu">
            <li class="nav-item">
                <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('crawl.form') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Crawl</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cv.form') }}" class="nav-link {{ request()->routeIs('cv.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Quản lý CV</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('crawl.history') }}" class="nav-link {{ request()->routeIs('crawl.history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Lịch sử</span>
                </a>
            </li>

            <!-- Mobile User Dropdown (sẽ được clone từ desktop) -->
            <li class="nav-item user-dropdown-mobile" style="display: none;" id="mobileUserDropdown">
                <!-- Clone từ desktop sẽ được xử lý bằng JS -->
            </li>
        </ul>

        <!-- Desktop User Dropdown -->
        <div class="user-dropdown" id="desktopUserDropdown">
            @auth
                <div class="user-toggle" id="userToggle">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>

                <div class="dropdown-menu" id="userDropdownMenu">
                    @if(strtolower(auth()->user()->role) === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item admin">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Dashboard</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    const toggleIcon = mobileToggle.querySelector('i');
    const mobileUserDropdown = document.getElementById('mobileUserDropdown');

    mobileToggle.addEventListener('click', function() {
        navbarMenu.classList.toggle('active');
        
        if (navbarMenu.classList.contains('active')) {
            toggleIcon.classList.remove('fa-bars');
            toggleIcon.classList.add('fa-times');
            
            // Clone desktop user dropdown cho mobile
            if (document.getElementById('desktopUserDropdown')) {
                const clone = document.getElementById('desktopUserDropdown').cloneNode(true);
                clone.id = 'mobileUserDropdownClone';
                mobileUserDropdown.innerHTML = '';
                mobileUserDropdown.appendChild(clone);
                mobileUserDropdown.style.display = 'block';
            }
        } else {
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            mobileUserDropdown.innerHTML = '';
            mobileUserDropdown.style.display = 'none';
        }
    });

    // Desktop dropdown toggle
    const userToggle = document.getElementById('userToggle');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userToggle && userDropdownMenu) {
        userToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('desktopUserDropdown').classList.toggle('active');
        });
    }

    // Close dropdown when click outside
    document.addEventListener('click', function(e) {
        const navbar = document.querySelector('.navbar');
        if (!navbar.contains(e.target)) {
            // Close mobile menu
            if (navbarMenu.classList.contains('active')) {
                navbarMenu.classList.remove('active');
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                mobileUserDropdown.innerHTML = '';
                mobileUserDropdown.style.display = 'none';
            }
            
            // Close desktop dropdown
            if (document.getElementById('desktopUserDropdown')?.classList.contains('active')) {
                document.getElementById('desktopUserDropdown').classList.remove('active');
            }
        }
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                navbarMenu.classList.remove('active');
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                mobileUserDropdown.innerHTML = '';
                mobileUserDropdown.style.display = 'none';
            }
        });
    });

    // Handle resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            navbarMenu.classList.remove('active');
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            mobileUserDropdown.innerHTML = '';
            mobileUserDropdown.style.display = 'none';
        }
    });
</script>