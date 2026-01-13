@extends('layouts.admin')

@section('title', 'Quản lý Ticket')

@section('content')
<div class="container-fluid">
    <div class="header mb-5 text-center">
        <h1 style="font-size: 2.6rem; margin-bottom: 12px;">
            <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #ff88ff);
                         background-size: 200% 200%;
                         animation: textGradient 8s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                Quản Lý Ticket Hỗ Trợ
            </span>
        </h1>
        <p class="stats opacity-85">
            Theo dõi và xử lý các yêu cầu hỗ trợ từ người dùng
        </p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người dùng</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td><strong>#{{ $t->id }}</strong></td>
                        <td>
                            <div style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $t->title }}
                            </div>
                        </td>
                        <td>{{ $t->user->name ?? 'N/A' }}</td>
                        <td>
                            <span class="priority-badge priority-{{ strtolower($t->priority) }}">
                                {{ $t->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ strtolower($t->status) }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $t) }}" 
                               class="action-btn">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="no-data">
                            <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.4;"></i>
                            <p>Chưa có ticket nào.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination mt-4">
        {{ $tickets->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes textGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .header {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        padding: 32px;
        border: 1px solid rgba(0, 180, 255, 0.15);
    }

    .table-wrapper {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: rgba(0, 180, 255, 0.08);
        padding: 18px 16px;
        text-align: left;
        font-size: 0.95rem;
        font-weight: 700;
        color: #00d4ff;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 18px 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 0.92rem;
        vertical-align: middle;
    }

    tr:hover {
        background: rgba(0, 180, 255, 0.08);
    }

    .priority-badge {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        border: 1px solid;
    }

    .priority-low    { background: rgba(0, 255, 150, 0.15); color: #00ffaa; border-color: rgba(0, 255, 150, 0.4); }
    .priority-medium { background: rgba(255, 200, 0, 0.15); color: #ffdd00; border-color: rgba(255, 200, 0, 0.4); }
    .priority-high   { background: rgba(255, 100, 100, 0.15); color: #ff6b6b; border-color: rgba(255, 100, 100, 0.4); }
    .priority-urgent { background: rgba(255, 50, 50, 0.2); color: #ff4444; border-color: rgba(255, 50, 50, 0.5); }

    .status-badge {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        border: 1px solid;
    }

    .status-open      { background: rgba(0, 180, 255, 0.15); color: #00b4ff; border-color: rgba(0, 180, 255, 0.4); }
    .status-inprogress{ background: rgba(255, 200, 0, 0.15); color: #ffdd00; border-color: rgba(255, 200, 0, 0.4); }
    .status-resolved  { background: rgba(0, 255, 150, 0.15); color: #00ffaa; border-color: rgba(0, 255, 150, 0.4); }
    .status-closed    { background: rgba(100, 100, 100, 0.3); color: #cccccc; border-color: rgba(150, 150, 150, 0.4); }

    .action-btn {
        background: rgba(0, 180, 255, 0.1);
        border: 1px solid #00b4ff;
        color: #00b4ff;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn:hover {
        background: rgba(0, 180, 255, 0.25);
        color: #00d4ff;
        transform: translateY(-2px);
    }

    .no-data {
        text-align: center;
        padding: 100px 20px;
        opacity: 0.7;
        font-size: 1.3rem;
    }

    .no-data i {
        font-size: 4rem;
        margin-bottom: 20px;
        display: block;
        opacity: 0.4;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 32px;
    }

    .pagination .page-item .page-link {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #bbd0ff;
        border-radius: 8px;
        margin: 0 4px;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background: #00b4ff;
        border-color: #00b4ff;
        color: #000;
        font-weight: 700;
    }

    .pagination .page-link:hover {
        background: rgba(0, 180, 255, 0.2);
        color: #fff;
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-header h1 {
            font-size: 2.4rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="header mb-5 text-center">
        <h1 style="font-size: 2.6rem; margin-bottom: 12px;">
            <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #ff88ff);
                         background-size: 200% 200%;
                         animation: textGradient 8s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                Quản Lý Ticket Hỗ Trợ
            </span>
        </h1>
        <p class="stats opacity-85">
            Theo dõi và xử lý các yêu cầu từ người dùng • Tổng: <strong>{{ $tickets->total() }}</strong>
        </p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người dùng</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td><strong>#{{ $t->id }}</strong></td>
                        <td>
                            <div style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $t->title }}
                            </div>
                        </td>
                        <td>{{ $t->user->name ?? 'N/A' }}</td>
                        <td>
                            <span class="priority-badge priority-{{ strtolower($t->priority) }}">
                                {{ $t->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ strtolower($t->status) }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $t) }}" 
                               class="action-btn">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="no-data">
                            <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.4;"></i>
                            <p>Chưa có ticket nào trong hệ thống.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination mt-4">
        {{ $tickets->links() }}
    </div>
</div>
@endsection