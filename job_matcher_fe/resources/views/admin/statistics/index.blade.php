@extends('layouts.admin')

@section('page-title', 'Job Market Analytics')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="header mb-5 text-center">
            <h1 style="font-size: 2.8rem; margin-bottom: 12px;">
                <span
                    style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
                         background-size: 200% 200%;
                         animation: textGradient 8s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                    Job Market Analytics
                </span>
            </h1>
            <p class="stats opacity-85">Thống kê toàn diện thị trường việc làm IT từ dữ liệu crawl</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid mb-5">
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-number">{{ number_format($totalJobs) }}</div>
                <div class="stat-label">Tổng Công Việc</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon today-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number">{{ number_format($todayJobs) }}</div>
                <div class="stat-label">Hôm Nay</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon week-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-number">{{ number_format($weekJobs) }}</div>
                <div class="stat-label">Tuần Này</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon month-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-number">{{ number_format($monthJobs) }}</div>
                <div class="stat-label">Tháng Này</div>
            </div>

            @if ($duplicateJobs > 0)
                <div class="stat-box duplicate-stat">
                    <div class="stat-icon duplicate-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number">{{ number_format($duplicateJobs) }}</div>
                    <div class="stat-label">Công Việc Trùng Lặp</div>
                </div>
            @endif
        </div>

        <!-- Top Keywords Chart -->
        <div class="chart-card mb-5">
            <h3 class="chart-title">
                <i class="fas fa-key"></i> Top 20 Từ Khoá Nổi Bật Trong Tiêu Đề Việc Làm
            </h3>
            <canvas id="keywordsChart"></canvas>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <!-- Jobs by Location -->
            <div class="chart-card">
                <h3 class="chart-title">
                    <i class="fas fa-map-marker-alt"></i> Phân Bố Công Việc Theo Địa Điểm
                </h3>
                <canvas id="locationChart"></canvas>
            </div>

            <!-- Salary Distribution -->
            <div class="chart-card">
                <h3 class="chart-title">
                    <i class="fas fa-money-bill-wave"></i> Phân Bố Mức Lương
                </h3>
                <canvas id="salaryChart"></canvas>
            </div>
        </div>

        <!-- Technology Trend Charts -->
        <div class="chart-card mt-5">
            <h3 class="chart-title">
                <i class="fas fa-chart-line"></i> Xu Hướng Công Nghệ (7 ngày gần nhất - Moving Average)
            </h3>
            <canvas id="trendChart"></canvas>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes textGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-box {
            background: rgba(15, 15, 25, 0.6);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(0, 180, 255, 0.25);
            border-radius: 20px;
            padding: 32px;
            text-align: center;
            transition: all 0.4s ease;
        }

        .stat-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 180, 255, 0.2);
            border-color: rgba(0, 180, 255, 0.5);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
            background: rgba(0, 180, 255, 0.15);
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #00b4ff;
        }

        .today-icon {
            background: rgba(255, 200, 0, 0.15);
            border-color: rgba(255, 200, 0, 0.3);
            color: #ffdd00;
        }

        .week-icon {
            background: rgba(0, 255, 150, 0.15);
            border-color: rgba(0, 255, 150, 0.3);
            color: #00ffaa;
        }

        .month-icon {
            background: rgba(255, 100, 255, 0.15);
            border-color: rgba(255, 100, 255, 0.3);
            color: #ff88ff;
        }

        /* Duplicate stat box */
        .duplicate-stat {
            border-color: rgba(255, 100, 100, 0.5) !important;
        }

        .duplicate-stat:hover {
            box-shadow: 0 20px 40px rgba(255, 100, 100, 0.2);
            border-color: rgba(255, 100, 100, 0.7) !important;
        }

        .duplicate-icon {
            background: rgba(255, 100, 100, 0.15) !important;
            border-color: rgba(255, 100, 100, 0.4) !important;
            color: #ff6b6b !important;
        }

        .stat-number {
            font-size: 3.2rem;
            font-weight: 800;
            color: #ffffff;
            margin: 16px 0;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.85;
            font-weight: 600;
        }

        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 28px;
            margin-top: 20px;
        }

        .chart-card {
            background: rgba(15, 15, 25, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .chart-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #00d4ff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        canvas {
            max-height: 420px;
            width: 100% !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-card {
                padding: 24px;
            }

            .chart-title {
                font-size: 1.4rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Màu sắc neon đồng bộ
        const neonColors = [
            '#00b4ff', '#00ffaa', '#ffdd00', '#ff88ff', '#88ddff',
            '#ff6b6b', '#aaffaa', '#ffaa88', '#88aaff', '#aaffff'
        ];

        // Top Keywords - Horizontal Bar
        new Chart(document.getElementById('keywordsChart'), {
            type: 'bar',
            data: {
                labels: @json($topKeywordLabels),
                datasets: [{
                    label: 'Số lần xuất hiện',
                    data: @json($topKeywordData),
                    backgroundColor: neonColors,
                    borderColor: neonColors,
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 25, 0.9)'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    }
                }
            }
        });

        // Location Chart - Bar
        new Chart(document.getElementById('locationChart'), {
            type: 'bar',
            data: {
                labels: @json($byLocationLabels),
                datasets: [{
                    label: 'Số lượng',
                    data: @json($byLocationData),
                    backgroundColor: neonColors,
                    borderColor: neonColors,
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 25, 0.9)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    }
                }
            }
        });

        // Salary Chart - Doughnut
        new Chart(document.getElementById('salaryChart'), {
            type: 'doughnut',
            data: {
                labels: @json($salaryLabels),
                datasets: [{
                    data: @json($salaryData),
                    backgroundColor: neonColors,
                    borderColor: neonColors,
                    borderWidth: 3,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#bbd0ff',
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 25, 0.9)'
                    }
                }
            }
        });

        // Trend Chart - Line with Moving Average
        const trendCtx = document.getElementById('trendChart');
        const datasets = [];

        const stackLabels = @json(array_keys($trendStacks));
        const colors = ['#00b4ff', '#ff6b6b', '#00ffaa', '#ffdd00', '#ff88ff', '#88ddff', '#aaffaa', '#ffaa88'];

        stackLabels.forEach((stack, index) => {
            const dailyData = @json($trendStacks)[stack]['daily'];
            const ma7Data = @json($trendStacks)[stack]['ma7'];

            // Daily line (thin, dotted)
            datasets.push({
                label: stack.toUpperCase() + ' (hàng ngày)',
                data: Object.values(dailyData),
                borderColor: colors[index],
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 3,
                tension: 0.4,
                fill: false
            });

            // Moving Average (thick, solid)
            datasets.push({
                label: stack.toUpperCase() + ' (MA7)',
                data: Object.values(ma7Data),
                borderColor: colors[index],
                backgroundColor: colors[index] + '30',
                borderWidth: 4,
                pointRadius: 4,
                tension: 0.4,
                fill: '+1'
            });
        });

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: Object.keys(@json($trendStacks)['php']['daily'] ??
            []), // dùng ngày từ stack đầu tiên
                datasets: datasets
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#bbd0ff'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 25, 0.9)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#bbd0ff'
                        }
                    }
                }
            }
        });
    </script>
@endpush
