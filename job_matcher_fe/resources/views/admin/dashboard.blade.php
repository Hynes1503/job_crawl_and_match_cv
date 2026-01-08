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

        .user-icon {
            background: rgba(0, 255, 150, 0.15);
            border-color: rgba(0, 255, 150, 0.3);
            color: #00ffaa;
        }

        .log-icon {
            background: rgba(255, 200, 0, 0.15);
            border-color: rgba(255, 200, 0, 0.3);
            color: #ffdd00;
        }

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

        .activity-section,
        .chart-section {
            background: rgba(15, 15, 25, 0.6);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(0, 180, 255, 0.25);
            border-radius: 16px;
            padding: 28px;
        }

        .section-title,
        .chart-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #00d4ff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-logs {
            max-height: 380px;
            overflow-y: auto;
        }

        .log-item {
            display: flex;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .log-item:last-child {
            border-bottom: none;
        }

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

        .chart-container {
            position: relative;
            height: 380px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2.1rem;
            }

            .stat-number {
                font-size: 2.2rem;
            }

            .stats-grid {
                gap: 16px;
            }

            .stat-box {
                padding: 20px;
            }

            .recent-logs,
            .chart-container {
                max-height: 300px;
                height: 300px;
            }
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
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Tổng Người Dùng</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon user-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ $crawlToday }}</div>
                <div class="stat-label">Crawl Hôm Nay</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon log-icon"><i class="fas fa-history"></i></div>
                <div class="stat-number">{{ $totalLogs }}</div>
                <div class="stat-label">Tổng Hoạt Động</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon"
                    style="background: rgba(255,100,100,0.15); border-color: rgba(255,100,100,0.3); color: #ff6b6b;">
                    <i class="fas fa-copy"></i>
                </div>
                <div class="stat-number">{{ $duplicateJobs }}</div>
                <div class="stat-label">Job Trùng (theo hash)</div>
            </div>
        </div>

        <!-- Recent Activity + Location Chart -->
        <div class="stats-grid">
            <!-- Recent Activity -->
            <div class="activity-section" style="grid-column: span 2;">
                <h2 class="section-title">
                    <i class="fas fa-stream"></i> Hoạt Động Gần Đây
                </h2>
                <div class="recent-logs">
                    @forelse($recentLogs as $log)
                        <div class="log-item">
                            <div class="log-avatar">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                            </div>
                            <div class="log-content">
                                <div class="log-user">
                                    {{ $log->user?->name ?? 'System' }}
                                    <span style="opacity: 0.7;">
                                        • {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
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

            <!-- Location Distribution Chart -->
            <div class="chart-section">
                <h2 class="chart-title">
                    <i class="fas fa-map-marker-alt"></i> Địa điểm thường xuyên xuất hiện
                </h2>
                <div class="chart-container">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('locationChart').getContext('2d');

            const labels = @json($locationLabels);
            const data = @json($locationData);

            // Gradient màu từ xanh dương sang xanh lá
            const gradientColors = [
                '#00b4ff', '#00c0ff', '#00ccff', '#00d8ff',
                '#00e4ff', '#00ffaa', '#00ef95', '#00df80',
                '#00cf6b', '#00bf56', '#00af41'
            ];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số lượng việc làm',
                        data: data,
                        backgroundColor: gradientColors.slice(0, labels.length),
                        borderColor: '#00b4ff',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#00ffaa',
                            bodyColor: '#bbd0ff'
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#bbd0ff',
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            },
                            ticks: {
                                color: '#bbd0ff',
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
