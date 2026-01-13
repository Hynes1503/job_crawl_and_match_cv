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

    <div class="mb-5">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Người dùng (tên/email)</label>
                <input type="text" name="user" class="form-control" value="{{ request('user') }}"
                       placeholder="Nhập tên hoặc email..."
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
            </div>

            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Hành động</label>
                <select name="action" class="form-select custom-select">
                    <option value="">Tất cả hành động</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>View</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
            </div>

            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn w-100"
                        style="background: #00b4ff; border: none; padding: 10px; color: #000; font-weight: 600;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        @if(request()->hasAny(['user', 'action', 'from_date', 'to_date']))
            <div class="mt-3 text-end">
                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary"
                   style="border: 1px solid rgba(255,255,255,0.3); color: #bbd0ff; padding: 8px 16px;">
                    <i class="fas fa-times me-2"></i>Xóa bộ lọc
                </a>
            </div>
        @endif
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
                        <th>Thời Gian</th>
                        <th style="text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hasTodayLogs = $logs->contains(fn($log) => $log->created_at->isToday());
                        $showTodayHeader = $hasTodayLogs;
                    @endphp

                    @if($showTodayHeader)
                        <tr>
                            <td colspan="6"
                                style="text-align: center; padding: 16px; font-weight: 600; color: #00ffaa; font-size: 1.1rem; background: rgba(0, 255, 170, 0.08);">
                                <i class="fas fa-clock me-2"></i>
                                HOẠT ĐỘNG HÔM NAY
                            </td>
                        </tr>
                    @endif

                    @foreach($logs as $log)
                        @if(!$loop->first && !$log->created_at->isSameDay($logs[$loop->iteration - 1]->created_at))
                            <tr>
                                <td colspan="6"
                                    style="padding: 0; height: 8px; background: linear-gradient(to right, transparent, #00b4ff, transparent); opacity: 0.5;">
                                </td>
                            </tr>
                        @endif

                        <tr class="{{ $log->created_at->isToday() ? 'today-log' : '' }}">
                            <td class="fw-bold">#{{ $log->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #00b4ff, #00ffaa); color: #000; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-right: 12px;">
                                        {{ $log->user ? strtoupper(substr($log->user->name ?? 'N/A', 0, 1)) : 'N' }}
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
                                <strong style="color: {{ $log->created_at->isToday() ? '#00ffaa' : '#bbd0ff' }};">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </strong>
                                @if($log->created_at->isToday())
                                    <small style="opacity: 0.6; display: block; color: #00ffaa;">(Hôm nay)</small>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-history fa-4x mb-4" style="opacity: 0.4;"></i>
            <p>Không tìm thấy bản ghi nào phù hợp với bộ lọc.</p>
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

    tr.today-log {
        background: rgba(0, 180, 255, 0.12) !important;
    }

    tr.today-log:hover {
        background: rgba(0, 255, 170, 0.18) !important;
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
        max-width: 420px;
        word-break: break-word;
        opacity: .85;
        font-size: 0.88rem;
    }

    .btn-delete {
        background: rgba(255, 50, 80, 0.2);
        border: 1px solid rgba(255, 50, 80, 0.4);
        color: #ff5080;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        background: rgba(255, 50, 80, 0.35);
        border-color: #ff5080;
        color: #fff;
        transform: translateY(-2px);
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pagination .page-link {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: #bbd0ff;
        border-radius: 8px;
        padding: 10px 16px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: rgba(0,180,255,0.2);
        color: #fff;
    }

    .pagination .page-item.active .page-link {
        background: #00b4ff;
        border-color: #00b4ff;
        color: #000;
        font-weight: 600;
    }

    .custom-select {
        background: rgba(0, 0, 0, 0.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        backdrop-filter: blur(4px);
        border-radius: 8px;
    }

    .custom-select option {
        background: #1a1a2e !important;
        color: #ffffff !important;
    }

    .custom-select:focus {
        border-color: #00b4ff !important;
        box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2) !important;
    }

    .no-data {
        text-align: center;
        padding: 120px 20px;
        opacity: .6;
        font-size: 1.3rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('tbody tr:not([colspan])').forEach((row, index) => {
            row.style.opacity = '0';
            row.style.animation = `fadeIn 0.6s ease forwards`;
            row.style.animationDelay = `${index * 0.05}s`;
        });
    });

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush