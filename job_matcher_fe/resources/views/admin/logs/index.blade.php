@extends('layouts.admin')

@section('page-title', 'System Logs')

@section('content')
<div class="container-fluid">
    <div class="header mb-5 text-center">
        <h1 style="font-size: 2.6rem; margin-bottom: 16px;">
            <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
                         background-size: 200% 200%;
                         animation: textGradient 6s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                Nhật Ký Hoạt Động
            </span>
        </h1>
        <p class="stats opacity-85">Tổng cộng <strong>{{ $logs->total() }}</strong> bản ghi hoạt động</p>
    </div>

    @if($logs->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người Dùng</th>
                        <th>Hành Động</th>
                        <th>Mô Tả</th>
                        <th>Địa Chỉ IP</th>
                        <th>Thời Gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="fw-bold">#{{ $log->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #00b4ff, #00ffaa); color: #000; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-right: 12px;">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'N' }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $log->user ? $log->user->name : 'N/A' }}</div>
                                        <div style="font-size: 0.82rem; opacity: 0.7;">ID: {{ $log->user_id ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status status-action">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="message">
                                {{ $log->description }}
                            </td>
                            <td>
                                <span style="font-family: monospace;">{{ $log->ip_address }}</span>
                            </td>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-history fa-4x mb-4" style="opacity: 0.4;"></i>
            <p>Chưa có hoạt động nào được ghi nhận.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    @keyframes textGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .table-wrapper {
        background: rgba(0, 0, 0, .45);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: 16px;
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: rgba(255, 255, 255, .05);
        padding: 18px 16px;
        text-align: left;
        font-size: 0.95rem;
        font-weight: 600;
        opacity: .9;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    td {
        padding: 18px 16px;
        border-top: 1px solid rgba(255, 255, 255, .08);
        font-size: 0.92rem;
        vertical-align: middle;
    }

    tr:hover {
        background: rgba(0, 180, 255, 0.08);
    }

    .status {
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-action {
        background: rgba(0, 255, 150, .2);
        color: #00ffaa;
        border: 1px solid rgba(0, 255, 150, 0.4);
    }

    .message {
        max-width: 400px;
        word-break: break-word;
        opacity: .85;
        font-size: 0.88rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
    }

    .pagination .page-link {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: #bbd0ff;
    }

    .pagination .page-item.active .page-link {
        background: #00b4ff;
        border-color: #00b4ff;
        color: #000;
    }

    .no-data {
        text-align: center;
        padding: 120px 20px;
        opacity: .6;
        font-size: 1.3rem;
    }

    .header .stats {
        font-size: 1.1rem;
        opacity: .85;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('tr').forEach((row, index) => {
            row.style.animation = `fadeIn 0.6s ease forwards`;
            row.style.animationDelay = `${index * 0.05}s`;
            row.style.opacity = '0';
        });
    });

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</script>
@endpush