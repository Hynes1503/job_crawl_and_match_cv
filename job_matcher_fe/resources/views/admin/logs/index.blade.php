@extends('layouts.admin')

@section('page-title', 'System Logs')

@section('content')
<div class="container-fluid">
    <!-- Thông báo Toast -->
    {{-- @if(session('success'))
        <div class="toast-notification success" id="toastNotification">
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">Thành công!</div>
                <div class="toast-message">{{ session('success') }}</div>
            </div>
            <button class="toast-close" onclick="closeToast()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-notification error" id="toastNotification">
            <div class="toast-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">Lỗi!</div>
                <div class="toast-message">{{ session('error') }}</div>
            </div>
            <button class="toast-close" onclick="closeToast()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif --}}

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

    <!-- Form Bộ lọc -->
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

        @if(request()->hasAny(['user', 'action', 'ip', 'from_date', 'to_date']))
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
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
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

        <!-- Phân trang -->
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

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    @keyframes progressBar {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }

    /* Toast Notification */
    .toast-notification {
        position: fixed;
        top: 24px;
        right: 24px;
        min-width: 350px;
        max-width: 450px;
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(12px);
        border-radius: 12px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        z-index: 9999;
        animation: slideIn 0.4s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .toast-notification.hiding {
        animation: slideOut 0.4s ease-out forwards;
    }

    .toast-notification.success {
        border-left: 4px solid #00ffaa;
    }

    .toast-notification.error {
        border-left: 4px solid #ff5080;
    }

    .toast-notification.success .toast-icon {
        color: #00ffaa;
        font-size: 1.5rem;
    }

    .toast-notification.error .toast-icon {
        color: #ff5080;
        font-size: 1.5rem;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 4px;
        color: #fff;
    }

    .toast-message {
        font-size: 0.9rem;
        opacity: 0.85;
        color: #ddd;
    }

    .toast-close {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.5);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 4px 8px;
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .toast-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    /* Progress bar cho toast */
    .toast-notification::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #00b4ff, #00ffaa);
        animation: progressBar 4s linear forwards;
        border-radius: 0 0 12px 12px;
    }

    .toast-notification.error::after {
        background: linear-gradient(90deg, #ff5080, #ff8a00);
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

    /* Button Delete */
    .btn-delete {
        background: rgba(255, 50, 80, 0.2);
        border: 1px solid rgba(255, 50, 80, 0.4);
        color: #ff5080;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        background: rgba(255, 50, 80, 0.3);
        border-color: #ff5080;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 50, 80, 0.3);
    }

    .btn-delete i {
        pointer-events: none;
    }

    /* Pagination */
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

    /* Fix select như trang users */
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

    .header .stats {
        font-size: 1.1rem;
        opacity: .85;
    }
</style>
@endpush

@push('scripts')
<script>
    // Toast notification auto-hide
    function closeToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 400);
        }
    }

    // Auto hide toast after 4 seconds
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            setTimeout(() => {
                closeToast();
            }, 4000);
        }

        // Table row animation
        document.querySelectorAll('tbody tr').forEach((row, index) => {
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
@endpu