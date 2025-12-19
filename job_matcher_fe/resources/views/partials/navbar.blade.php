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
                <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Job Matcher AI
                </span>
            </h2>
        </div>

        <div style="display: flex; gap: 32px; align-items: center;">
            <a href="{{ route('dashboard') ?? '/' }}" style="color: #fff; text-decoration: none; font-weight: 600; opacity: .9; transition: opacity .3s;">Dashboard</a>
            <a href="{{ route('crawl.form') ?? '/crawl' }}" style="color: #fff; text-decoration: none; font-weight: 600; opacity: .9; transition: opacity .3s;">Crawl Jobs</a>
            <a href="{{ route('crawl.history') }}" style="color: #00b4ff; text-decoration: none; font-weight: 700; transition: color .3s;">Lịch sử Crawl</a>

            @auth
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #ff6b6b; cursor: pointer; font-weight: 600; transition: color .3s;">
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" style="color: #00b4ff; font-weight: 600; transition: color .3s;">Đăng nhập</a>
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
</style>