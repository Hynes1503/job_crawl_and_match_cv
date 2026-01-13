@extends('layouts.app')
@section('title', 'Phản ánh của tôi')

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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 32px;
            animation: fadeIn 0.6s ease;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .create-btn {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .create-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 255, 0.4);
            color: #000;
        }

        /* Table Container */
        .table-container {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 32px;
            animation: fadeIn 0.6s ease 0.2s backwards;
        }

        /* Modern Table */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .modern-table thead th {
            background: rgba(0, 0, 0, 0.3);
            padding: 16px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
            border: none;
        }

        .modern-table thead th:first-child {
            border-radius: 12px 0 0 12px;
        }

        .modern-table thead th:last-child {
            border-radius: 0 12px 12px 0;
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
            padding: 20px 16px;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.95rem;
            vertical-align: middle;
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

        /* Ticket Title */
        .ticket-title {
            font-weight: 700;
            color: #fff;
            font-size: 1.05rem;
        }

        /* Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        /* Category Badges */
        .badge-category {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.3);
        }

        /* Priority Badges */
        .badge-low {
            background: rgba(100, 100, 100, 0.15);
            color: #888;
            border: 1px solid rgba(100, 100, 100, 0.3);
        }

        .badge-medium {
            background: rgba(255, 200, 0, 0.15);
            color: #ffdd00;
            border: 1px solid rgba(255, 200, 0, 0.3);
        }

        .badge-high {
            background: rgba(255, 100, 0, 0.15);
            color: #ff9a56;
            border: 1px solid rgba(255, 100, 0, 0.3);
        }

        .badge-urgent {
            background: rgba(255, 0, 100, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 0, 100, 0.3);
        }

        /* Status Badges */
        .badge-open {
            background: rgba(255, 100, 100, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 100, 100, 0.3);
        }

        .badge-processing {
            background: rgba(255, 200, 0, 0.15);
            color: #ffdd00;
            border: 1px solid rgba(255, 200, 0, 0.3);
        }

        .badge-closed {
            background: rgba(0, 255, 150, 0.15);
            color: #00ffaa;
            border: 1px solid rgba(0, 255, 150, 0.3);
        }

        .badge-resolved {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.3);
        }

        /* Action Button */
        .view-btn {
            padding: 8px 16px;
            background: rgba(0, 180, 255, 0.15);
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #00b4ff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-btn:hover {
            background: rgba(0, 180, 255, 0.25);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
        }

        .empty-state i {
            font-size: 6rem;
            color: #00b4ff;
            opacity: 0.2;
            margin-bottom: 24px;
            display: block;
        }

        .empty-state h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 12px;
            opacity: 0.8;
        }

        .empty-state p {
            opacity: 0.6;
            font-size: 1.1rem;
            margin-bottom: 32px;
        }

        .pagination {
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 255, 0.2);
            color: #00b4ff;
            border-radius: 10px;
            padding: 10px 16px;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background: rgba(0, 180, 255, 0.2);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border-color: transparent;
            color: #000;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 180, 255, 0.3);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(120deg, #00b4ff, #00ffaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.7;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .create-btn {
                width: 100%;
                justify-content: center;
            }

            .modern-table {
                font-size: 0.9rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 12px 10px;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 24px;
            }

            .page-title {
                font-size: 2rem;
            }

            .table-container {
                padding: 20px;
                overflow-x: auto;
            }

            .modern-table {
                min-width: 600px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.8rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-headset"></i>
                Phản Ánh Của Tôi
            </h1>
            <a href="#" class="create-btn" onclick="openSupportModal()">
                <i class="fas fa-plus-circle"></i>
                Gửi phản ánh mới
            </a>
        </div>
    </div>

   @if ($tickets->count() > 0)
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $tickets->total() }}</div>
                <div class="stat-label">Tổng phản ánh</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tickets->where('status', 'open')->count() }}</div>
                <div class="stat-label">Đang mở</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tickets->where('status', 'processing')->count() }}</div>
                <div class="stat-label">Đang xử lý</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $tickets->where('status', 'closed')->count() }}</div>
                <div class="stat-label">Đã đóng</div>
            </div>
        </div>
    @endif

    <div class="table-container">
        @if ($tickets->count() > 0)
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $t)
                        <tr>
                            <td>
                                <div class="ticket-title">{{ $t->title }}</div>
                            </td>
                            <td>
                                <span class="badge badge-category">
                                    <i class="fas fa-tag"></i>
                                    {{ ucfirst($t->category) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($t->priority) }}">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ ucfirst($t->priority) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($t->status) {
                                        'open' => 'badge-open',
                                        'processing' => 'badge-processing',
                                        'closed' => 'badge-closed',
                                        'resolved' => 'badge-resolved',
                                        default => 'badge-open',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                            <td>
                                <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                                    <i class="far fa-calendar-alt" style="margin-right: 6px; color: #00b4ff;"></i>
                                    {{ $t->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('support.show', $t) }}" class="view-btn">
                                    <i class="fas fa-eye"></i>
                                    Xem
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Chưa Có Phản Ánh</h3>
                <p>Bạn chưa gửi phản ánh nào. Hãy cho tôi biết ý kiến của bạn để giúp hệ thống phát triển</p>
                <a href="#" class="create-btn" onclick="openSupportModal()">
                    Gửi phản ánh mới
                </a>
            </div>
        @endif
    </div>
    @include('partials.support-modal')
@endsection
