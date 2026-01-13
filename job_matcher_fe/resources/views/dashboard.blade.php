@extends('layouts.app')

@section('title', 'Dashboard - Job Matcher AI')

@push('styles')
    <style>
        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(0, 180, 255, 0.3); }
            50% { box-shadow: 0 0 40px rgba(0, 180, 255, 0.6); }
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 24px;
            padding: 48px 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 180, 255, 0.15), transparent);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.2rem;
            font-weight: 900;
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .hero-content p {
            font-size: 1.3rem;
            opacity: 0.85;
            margin-bottom: 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }
        .stats-card {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 32px;
        }

        .stats-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .stats-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
            margin: 0;
        }

        .stats-header i {
            font-size: 1.8rem;
            color: #00b4ff;
        }

        .mini-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .mini-stat {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 255, 0.2);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .mini-stat:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 180, 255, 0.5);
            background: rgba(0, 180, 255, 0.08);
        }

        .mini-stat-icon {
            font-size: 2.2rem;
            margin-bottom: 12px;
        }

        .mini-stat-number {
            font-size: 2.4rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .mini-stat-label {
            font-size: 0.9rem;
            opacity: 0.7;
            font-weight: 500;
        }
        .stat-primary { color: #00b4ff; }
        .stat-warning { color: #ffdd00; }
        .stat-success { color: #00ffaa; }
        .stat-danger { color: #ff6b6b; }
        .stat-purple { color: #b084ff; }
        .stat-orange { color: #ff9a56; }
        .actions-panel {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 32px;
        }

        .actions-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .actions-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
            margin: 0;
        }

        .action-item {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.08), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.25);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .action-item:last-child {
            margin-bottom: 0;
        }

        .action-item:hover {
            transform: translateX(8px);
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.15), rgba(0, 255, 170, 0.1));
            border-color: rgba(0, 180, 255, 0.5);
            animation: glow 2s ease infinite;
        }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: rgba(0, 180, 255, 0.15);
            border: 1px solid rgba(0, 180, 255, 0.3);
            flex-shrink: 0;
        }

        .action-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .action-content p {
            font-size: 0.88rem;
            opacity: 0.7;
            margin: 0;
        }

        /* Recent Activity Section */
        .activity-section {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 32px;
        }

        .activity-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .activity-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .activity-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
            margin: 0;
        }

        .activity-title i {
            font-size: 1.8rem;
            color: #00b4ff;
        }

        .view-all-btn {
            background: rgba(0, 180, 255, 0.1);
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #00b4ff;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .view-all-btn:hover {
            background: rgba(0, 180, 255, 0.2);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
        }

        /* Modern Table */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .modern-table thead th {
            background: rgba(0, 0, 0, 0.3);
            padding: 14px 16px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
            border: none;
        }

        .modern-table tbody tr {
            background: rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(0, 180, 255, 0.08);
            transform: translateX(4px);
        }

        .modern-table tbody td {
            padding: 18px 16px;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
        }

        .modern-table tbody td:first-child {
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .modern-table tbody td:last-child {
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
            letter-spacing: 0.3px;
        }

        .badge-success { background: rgba(0, 255, 150, 0.15); color: #00ffaa; border: 1px solid rgba(0, 255, 150, 0.3); }
        .badge-warning { background: rgba(255, 200, 0, 0.15); color: #ffdd00; border: 1px solid rgba(255, 200, 0, 0.3); }
        .badge-danger { background: rgba(255, 100, 100, 0.15); color: #ff6b6b; border: 1px solid rgba(255, 100, 100, 0.3); }

        .highlight-number {
            font-weight: 800;
            color: #00d4ff;
            font-size: 1.05rem;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i {
            font-size: 5rem;
            opacity: 0.2;
            margin-bottom: 24px;
            display: block;
        }

        .empty-state h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 12px;
            opacity: 0.7;
        }

        .empty-state p {
            opacity: 0.5;
            margin-bottom: 24px;
        }

        .cta-btn {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border: none;
            color: #000;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 255, 0.4);
            color: #000;
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .mini-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }
            .hero-content p {
                font-size: 1.1rem;
            }
            .mini-stats-grid {
                grid-template-columns: 1fr;
            }
            .activity-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="hero-section">
        <div class="hero-content">
            <h1>Xin chào, {{ auth()->user()->name }}!</h1>
            <p>Chào mừng trở lại với hệ thống tìm việc thông minh của bạn</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="stats-card">
            <div class="stats-header">
                <i class="fas fa-chart-line"></i>
                <h2>Tổng Quan Hoạt Động</h2>
            </div>
            <div class="mini-stats-grid">
                <div class="mini-stat">
                    <div class="mini-stat-icon stat-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="mini-stat-number">{{ $stats['total_cvs'] }}</div>
                    <div class="mini-stat-label">CV Đã Upload</div>
                </div>

                <div class="mini-stat">
                    <div class="mini-stat-icon stat-purple">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="mini-stat-number">{{ $stats['total_crawls'] }}</div>
                    <div class="mini-stat-label">Tổng Lần Crawl</div>
                </div>

                <div class="mini-stat">
                    <div class="mini-stat-icon stat-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="mini-stat-number">{{ $stats['pending_crawls'] }}</div>
                    <div class="mini-stat-label">Đang Chờ</div>
                </div>

                <div class="mini-stat">
                    <div class="mini-stat-icon stat-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="mini-stat-number">{{ $stats['completed_crawls'] }}</div>
                    <div class="mini-stat-label">Hoàn Thành</div>
                </div>

                <div class="mini-stat">
                    <div class="mini-stat-icon stat-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="mini-stat-number">{{ $stats['failed_crawls'] }}</div>
                    <div class="mini-stat-label">Thất Bại</div>
                </div>

                <div class="mini-stat">
                    <div class="mini-stat-icon stat-orange">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="mini-stat-number">{{ number_format($stats['total_jobs_found']) }}</div>
                    <div class="mini-stat-label">Việc Làm</div>
                </div>
            </div>
        </div>

        <div class="actions-panel">
            <div class="actions-header">
                <i class="fas fa-bolt"></i>
                <h2>Thao Tác Nhanh</h2>
            </div>

            <a href="{{ route('cv.form') }}" class="action-item">
                <div class="action-icon stat-primary">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="action-content">
                    <h3>Upload CV</h3>
                    <p>Tải lên CV mới để bắt đầu</p>
                </div>
            </a>

            <a href="{{ route('match.cv.form') }}" class="action-item">
                <div class="action-icon stat-success">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-content">
                    <h3>Crawl Việc Làm</h3>
                    <p>Tìm kiếm công việc phù hợp</p>
                </div>
            </a>

            <a href="{{ route('crawl.history') }}" class="action-item">
                <div class="action-icon stat-purple">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-content">
                    <h3>Lịch Sử</h3>
                    <p>Xem lại các lần crawl</p>
                </div>
            </a>
        </div>
    </div>
    <div class="activity-section">
        <div class="activity-header">
            <div class="activity-title">
                <i class="fas fa-clock"></i>
                <h2>Hoạt Động Gần Đây</h2>
            </div>
            @if($recentCrawls->count() > 0)
                <a href="{{ route('crawl.history') }}" class="view-all-btn">
                    Xem Tất Cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            @endif
        </div>

        @if($recentCrawls->count() > 0)
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Thời Gian</th>
                        <th>Từ Khóa</th>
                        <th>Địa Điểm</th>
                        <th>Số Việc</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCrawls as $run)
                        <tr>
                            <td>
                                <i class="far fa-calendar-alt mr-2 stat-primary"></i>
                                {{ $run->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <strong>{{ $run->parameters['keyword'] ?? '-' }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt mr-2 stat-danger"></i>
                                {{ $run->parameters['location'] ?? 'Tất cả' }}
                            </td>
                            <td>
                                <span class="highlight-number">
                                    {{ $run->jobs_crawled ? number_format($run->jobs_crawled) : '0' }}
                                </span>
                            </td>
                            <td>
                                @if($run->status == 'completed')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i> Thành công
                                    </span>
                                @elseif($run->status == 'running')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-spinner mr-1"></i> Đang chạy
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Thất bại
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Chưa Có Hoạt Động</h3>
                <p>Bạn chưa thực hiện lần crawl nào. Hãy bắt đầu ngay!</p>
                <a href="{{ route('dashboard') }}" class="cta-btn">
                 Bắt Đầu Crawl Ngay
                </a>
            </div>
        @endif
    </div>
@endsection