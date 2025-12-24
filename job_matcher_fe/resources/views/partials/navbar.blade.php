<nav class="navbar" style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    padding: 20px 40px;
    backdrop-filter: blur(12px);
    background: rgba(0,0,0,.6);
    border-bottom: 1px solid rgba(255,255,255,.1);
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
">
    <div style="max-width: 1300px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #fff; color: #000; font-weight: 800; font-size: 22px; display: flex; align-items: center; justify-content: center;">
                JM
            </div>
            <h2 style="margin: 0; font-size: 1.6rem; font-weight: 700;">
                <span class="logo-text" style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Job Matcher AI
                </span>
            </h2>
        </div>

        <div style="display: flex; gap: 32px; align-items: center;">
            <a href="{{ route('cv.form')}}" class="nav-link" style="color: #fff; text-decoration: none; font-weight: 600; opacity: .9; transition: opacity .3s;">CV</a>
            <a href="{{ route('dashboard')}}" class="nav-link" style="color: #fff; text-decoration: none; font-weight: 600; opacity: .9; transition: opacity .3s;">Crawl Jobs</a>
            <a href="{{ route('crawl.history') }}" class="nav-link" style="color: #00b4ff; text-decoration: none; font-weight: 700; transition: color .3s;">Lịch sử Crawl</a>

            @auth
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; color: #ff6b6b; cursor: pointer; font-weight: 600; transition: color .3s;">
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link" style="color: #00b4ff; font-weight: 600; transition: color .3s;">Đăng nhập</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    @keyframes textGradient { 
        0%{background-position:0% 50%} 
        50%{background-position:100% 50%} 
        100%{background-position:0% 50%} 
    }

    .logo-text {
        position: relative;
        display: inline-block;
    }

    .logo-text::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 180, 255, 0.8), rgba(255, 255, 255, 1), rgba(0, 180, 255, 0.8), transparent);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        animation: shimmer 3s ease-in-out infinite;
        z-index: 1;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(0%); }
        100% { transform: translateX(100%); }
    }

    .nav-link {
        position: relative;
        overflow: hidden;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), 
            rgba(0, 180, 255, 0.4) 0%, 
            rgba(255, 255, 255, 0.2) 30%, 
            transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .nav-link:hover::before {
        opacity: 1;
    }

    .nav-link:hover {
        opacity: 1 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            link.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                this.style.setProperty('--x', x + '%');
                this.style.setProperty('--y', y + '%');
            });
        });
    });
</script>