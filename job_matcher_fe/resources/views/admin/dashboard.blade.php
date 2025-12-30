@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@push('styles')
<style>
    .dashboard-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .dashboard-header h1 {
        font-size: 2.4rem;
        font-weight: 800;
        background: linear-gradient(120deg, #00b4ff, #00ffaa, #00b4ff);
        background-size: 200% 200%;
        animation: textGradient 8s ease infinite;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
    }

    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.8;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 36px;
    }

    .stat-box {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(0, 180, 255, 0.25);
        border-radius: 16px;
        padding: 24px;
        transition: all 0.4s ease;
    }

    .stat-box:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 180, 255, 0.15);
        border-color: rgba(0, 180, 255, 0.5);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 16px;
        background: rgba(0, 180, 255, 0.15);
        border: 1px solid rgba(0, 180, 255, 0.3);
        color: #00b4ff;
    }

    .admin-icon { background: rgba(255, 100, 100, 0.15); border-color: rgba(255, 100, 100, 0.3); color: #ff6b6b; }
    .user-icon { background: rgba(0, 255, 150, 0.15); border-color: rgba(0, 255, 150, 0.3); color: #00ffaa; }
    .log-icon { background: rgba(255, 200, 0, 0.15); border-color: rgba(255, 200, 0, 0.3); color: #ffdd00; }

    .stat-number {
        font-size: 2.4rem;
        font-weight: 800;
        margin: 12px 0;
        color: #ffffff;
    }

    .stat-label {
        font-size: 0.95rem;
        opacity: 0.8;
        font-weight: 500;
    }

    .activity-section {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 28px;
    }

    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #00d4ff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recent-logs {
        max-height: 320px;
        overflow-y: auto;
    }

    .log-item {
        display: flex;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .log-item:last-child { border-bottom: none; }

    .log-item:hover {
        background: rgba(0, 180, 255, 0.08);
        border-radius: 10px;
        padding-left: 10px;
        padding-right: 10px;
        margin: 0 -10px;
    }

    .log-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00b4ff, #00ffaa);
        color: #000;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-right: 14px;
        flex-shrink: 0;
    }

    .log-content {
        flex: 1;
        font-size: 0.95rem;
    }

    .log-user {
        font-weight: 600;
        color: #ffffff;
    }

    .log-action {
        opacity: 0.8;
        margin-top: 3px;
        font-size: 0.9rem;
    }

    .log-time {
        font-size: 0.8rem;
        opacity: 0.6;
        white-space: nowrap;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-top: 36px;
    }

    .quick-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #bbd0ff;
        font-weight: 600;
        transition: all 0.4s ease;
        backdrop-filter: blur(8px);
    }

    .quick-btn i {
        font-size: 1.8rem;
        margin-bottom: 10px;
        display: block;
        opacity: 0.9;
    }

    .quick-btn:hover {
        background: rgba(0, 180, 255, 0.15);
        border-color: #00b4ff;
        color: #ffffff;
        transform: translateY(-4px);
    }

    .quick-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .dashboard-header h1 { font-size: 2.1rem; }
        .stat-number { font-size: 2.2rem; }
        .stat-icon { width: 50px; height: 50px; font-size: 1.4rem; }
        .stats-grid { gap: 16px; }
        .stat-box { padding: 20px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Chào mừng quay lại! Tổng quan hệ thống Job Matcher AI</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number">{{ \App\Models\User::count() }}</div>
            <div class="stat-label">Tổng Người Dùng</div>
        </div>

        <div class="stat-box">
            <div class="stat-icon admin-icon"><i class="fas fa-crown"></i></div>
            <div class="stat-number">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
            <div class="stat-label">Quản Trị Viên</div>
        </div>

        <div class="stat-box">
            <div class="stat-icon user-icon"><i class="fas fa-user"></i></div>
            <div class="stat-number">{{ \App\Models\User::where('role', 'user')->count() }}</div>
            <div class="stat-label">Người Dùng Thường</div>
        </div>

        <div class="stat-box">
            <div class="stat-icon log-icon"><i class="fas fa-history"></i></div>
            <div class="stat-number">{{ \App\Models\Log::count() }}</div>
            <div class="stat-label">Tổng Hoạt Động</div>
        </div>
    </div>

    <!-- Recent Activity + Quick Actions -->
    <div class="stats-grid">
        <!-- Recent Activity -->
        <div class="activity-section" style="grid-column: span 2;">
            <h2 class="section-title">
                <i class="fas fa-stream"></i> Hoạt Động Gần Đây
            </h2>
            <div class="recent-logs">
                @forelse(\App\Models\Log::with('user')->latest()->take(6)->get() as $log)
                    <div class="log-item">
                        <div class="log-avatar">
                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                        </div>
                        <div class="log-content">
                            <div class="log-user">
                                {{ $log->user ? $log->user->name : 'System' }}
                                <span style="opacity: 0.7; font-weight: normal;">• {{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                            </div>
                            <div class="log-action">{{ $log->description }}</div>
                        </div>
                        <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-center py-4 opacity-50">
                        <p>Chưa có hoạt động nào</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="activity-section">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i> Hành Động Nhanh
            </h2>
            <div class="quick-grid">
                <a href="{{ route('admin.users') }}" class="quick-btn">
                    <i class="fas fa-users"></i>
                    Người Dùng
                </a>
                <a href="{{ route('admin.logs.index') }}" class="quick-btn">
                    <i class="fas fa-history"></i>
                    Nhật Ký
                </a>
                <a href="{{ route('admin.deleted.crawls') }}" class="quick-btn">
                    <i class="fas fa-trash-restore"></i>
                    Crawl Đã Xóa
                </a>
                <div class="quick-btn disabled">
                    <i class="fas fa-cog"></i>
                    Cài Đặt
                </div>
            </div>
        </div>
    </div>
</div>
@endsection