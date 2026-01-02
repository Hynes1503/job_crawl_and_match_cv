<nav class="navbar" style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    padding: 20px 40px;
    backdrop-filter: blur(16px);
    background: rgba(10, 10, 20, 0.65);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
">
    <div style="max-width: 1300px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
        <!-- Logo -->
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="
                width: 48px; 
                height: 48px; 
                border-radius: 50%; 
                background: linear-gradient(135deg, #00b4ff, #00ffaa); 
                color: #000; 
                font-weight: 800; 
                font-size: 22px; 
                display: flex; 
                align-items: center; 
                justify-content: center;
                box-shadow: 0 0 20px rgba(0, 180, 255, 0.4);
            ">
                JM
            </div>
            <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800;">
                <span class="logo-text" style="
                    background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
                    background-size: 200% 200%;
                    animation: textGradient 8s ease infinite;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                ">
                    Job Matcher AI
                </span>
            </h2>
        </div>

        <!-- Navigation Links + User Avatar -->
        <div style="display: flex; gap: 36px; align-items: center;">
            <a href="{{ route('cv.form') }}" class="nav-link">CV</a>
            <a href="{{ route('dashboard') }}" class="nav-link">Crawl Jobs</a>
            <a href="{{ route('crawl.history') }}" class="nav-link active">Lịch sử Crawl</a>

            @auth
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="dropdown-menu">
                        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: left;">
                            <div style="font-weight: 600; color: #fff;">{{ auth()->user()->name }}</div>
                            <div style="font-size: 0.9rem; opacity: 0.7; margin-top: 4px;">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                <i class="fas fa-crown" style="margin-right: 12px; color: #ffdd00;"></i> Quản trị hệ thống
                            </a>
                        @endif

                        <!-- Nút Đăng xuất với confirm -->
                        <button type="button" class="dropdown-item logout-btn" onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt" style="margin-right: 12px; color: #ff6b6b;"></i> Đăng xuất
                        </button>

                        <!-- Form ẩn để submit logout thật -->
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="nav-link login-btn">Đăng nhập</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    @keyframes textGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .nav-link {
        color: #bbd0ff;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.05rem;
        padding: 10px 16px;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        opacity: 0.9;
    }

    .nav-link.active,
    .nav-link:hover {
        opacity: 1;
        color: #ffffff;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), 
            rgba(0, 180, 255, 0.4) 0%, 
            rgba(0, 255, 150, 0.2) 40%, 
            transparent 70%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .nav-link:hover::before {
        opacity: 1;
    }

    /* User Avatar & Dropdown */
    .user-dropdown {
        position: relative;
        cursor: pointer;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00b4ff, #00ffaa);
        color: #000;
        font-weight: 800;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 20px rgba(0, 180, 255, 0.3);
        transition: all 0.3s ease;
        user-select: none;
    }

    .user-avatar:hover {
        transform: scale(1.1);
        box-shadow: 0 0 30px rgba(0, 180, 255, 0.5);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 12px;
        background: rgba(15, 15, 25, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        min-width: 220px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        pointer-events: none;
        z-index: 1000;
    }

    .user-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: all;
    }

    .dropdown-item {
        display: block;
        padding: 14px 20px;
        color: #bbd0ff;
        text-decoration: none;
        font-size: 0.98rem;
        transition: all 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: rgba(0, 180, 255, 0.15);
        color: #ffffff;
    }

    .logout-btn {
        color: #ff6b6b !important;
    }

    .logout-btn:hover {
        background: rgba(255, 107, 107, 0.15) !important;
    }

    .login-btn {
        background: rgba(0, 180, 255, 0.15);
        border: 1px solid #00b4ff;
        padding: 10px 24px !important;
        border-radius: 12px;
        font-weight: 600;
    }

    .login-btn:hover {
        background: #00b4ff;
        color: #000 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('userDropdown');

        if (dropdown) {
            const avatar = dropdown.querySelector('.user-avatar');
            avatar.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });
        }

        // Đóng dropdown khi click ngoài
        document.addEventListener('click', function() {
            if (dropdown) {
                dropdown.classList.remove('active');
            }
        });

        // Hiệu ứng radial glow trên nav-link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                this.style.setProperty('--x', x + '%');
                this.style.setProperty('--y', y + '%');
            });
        });
    });

    // Hàm xác nhận đăng xuất
    function confirmLogout(event) {
        event.stopPropagation(); // Ngăn đóng dropdown ngay lập tức

        if (confirm('Bạn có chắc chắn muốn đăng xuất khỏi tài khoản không?')) {
            document.getElementById('logoutForm').submit();
        } else {
            // Nếu hủy, vẫn giữ dropdown mở (tùy chọn)
            document.getElementById('userDropdown').classList.add('active');
        }
    }
</script>