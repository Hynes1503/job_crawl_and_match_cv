@extends('layouts.admin')

@section('page-title', 'Quản Lý Site Selectors')

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
        table-layout: auto;
    }

    th {
        background: rgba(255, 255, 255, .05);
        padding: 18px 14px;
        text-align: left;
        font-size: 0.95rem;
        font-weight: 600;
        opacity: .9;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    td {
        padding: 16px 14px;
        border-top: 1px solid rgba(255, 255, 255, .08);
        vertical-align: middle;
        font-size: 0.92rem;
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
        border: 1px solid rgba(255,255,255,0.3);
    }

    .status-css {
        background: rgba(0, 180, 255, .2);
        color: #00b4ff;
        border-color: rgba(0, 180, 255, 0.4);
    }

    .status-id {
        background: rgba(255, 200, 0, .2);
        color: #ffdd00;
        border-color: rgba(255, 200, 0, 0.4);
    }

    .status-xpath {
        background: rgba(0, 255, 150, .2);
        color: #00ffaa;
        border-color: rgba(0, 255, 150, 0.4);
    }

    .status-class {
        background: rgba(255, 100, 255, .2);
        color: #ff88ff;
        border-color: rgba(255, 100, 255, 0.4);
    }

    .status-name {
        background: rgba(100, 200, 255, .2);
        color: #88ddff;
        border-color: rgba(100, 200, 255, 0.4);
    }

    .action-btn.save-btn {
        background: none;
        border: 1px solid #00ffaa;
        color: #00ffaa;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .action-btn.save-btn:hover {
        background: rgba(0, 255, 150, .2);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 255, 150, 0.3);
        color: #00ffdd;
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

    .no-data {
        text-align: center;
        padding: 120px 20px;
        opacity: .6;
        font-size: 1.3rem;
    }

    /* Nút Preview TopCV */
    .topcv-preview {
        text-align: center;
        margin-bottom: 32px;
    }

    .preview-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 14px 24px;
        background: rgba(0, 180, 255, 0.15);
        border: 1px solid rgba(0, 180, 255, 0.3);
        border-radius: 12px;
        color: #00b4ff;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(8px);
    }

    .preview-btn:hover {
        background: rgba(0, 180, 255, 0.25);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 180, 255, 0.3);
    }

    .preview-btn i {
        font-size: 1.3rem;
    }

    @media (max-width: 768px) {
        .toast-notification {
            min-width: 300px;
            right: 12px;
            top: 12px;
        }

        .preview-btn {
            padding: 12px 20px;
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="header mb-5 text-center">
        <h1 style="font-size: 2.4rem; margin-bottom: 12px;">
            <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
                         background-size: 200% 200%;
                         animation: textGradient 6s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                Quản Lý Site Selectors
            </span>
        </h1>
        <p class="stats opacity-85">
            Chỉnh sửa CSS selectors cho các trang crawl • Tổng cộng <strong>{{ $selectors->total() }}</strong> bản ghi
        </p>
    </div>

    <!-- Nút mở TopCV IT Jobs trong tab mới -->
    <div class="topcv-preview mb-4" style="text-align: center;">
        <a href="https://www.topcv.vn/viec-lam-it" 
           target="_blank" 
           rel="noopener noreferrer"
           class="preview-btn"
           title="Mở TopCV Việc làm IT trong tab mới để kiểm tra selectors">
            <i class="fas fa-external-link-alt"></i>
            Xem TopCV Việc làm IT
        </a>
    </div>

    @if($selectors->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Trang Web</th>
                        <th style="width: 12%;">Loại Trang</th>
                        <th style="width: 15%;">Khóa Phần Tử</th>
                        <th style="width: 10%;">Loại Selector</th>
                        <th style="width: 25%;">Giá Trị Selector</th>
                        <th style="width: 15%;">Mô Tả</th>
                        <th style="width: 8%;">Trạng Thái</th>
                        <th style="width: 8%;">Phiên Bản</th>
                        <th style="width: 7%;">Lưu Thay Đổi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectors as $selector)
                        <tr>
                            <td><strong>{{ $selector->site }}</strong></td>
                            <td>{{ $selector->page_type }}</td>
                            <td style="font-family: monospace; word-break: break-all;"><code>{{ $selector->element_key }}</code></td>
                            <td>
                                @php
                                    $type = strtolower($selector->selector_type);
                                    $label = match($type) {
                                        'css'     => 'CSS Selector',
                                        'id'      => 'ID Selector',
                                        'xpath'   => 'XPath',
                                        'class'   => 'Class Selector',
                                        'name'    => 'Name Selector',
                                        default   => strtoupper($type)
                                    };
                                @endphp
                                <span class="status status-{{ $type }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td style="word-break: break-all;">
                                <form action="{{ route('admin.site-selectors.update', $selector) }}" method="POST" id="form-{{ $selector->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="selector_value" value="{{ $selector->selector_value }}"
                                           style="width:100%; padding:10px 12px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); border-radius:10px; color:#fff; font-family:monospace; font-size:0.92rem; word-break:break-all;">
                            </td>
                            <td style="word-break: break-all;">
                                    <input type="text" name="description" value="{{ $selector->description }}"
                                           placeholder="Mô tả ngắn (tùy chọn)"
                                           style="width:100%; padding:10px 12px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); border-radius:10px; color:#fff; word-break:break-all;">
                            </td>
                            <td style="text-align:center;">
                                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; justify-content:center;">
                                    <input type="checkbox" name="is_active" value="1" {{ $selector->is_active ? 'checked' : '' }}
                                           style="width:20px; height:20px; cursor:pointer;">
                                    <span style="font-size:0.9rem; font-weight:600;">
                                        {{ $selector->is_active ? 'Bật' : 'Tắt' }}
                                    </span>
                                </label>
                            </td>
                            <td style="text-align:center;">
                                    <input type="text" name="version" value="{{ $selector->version }}"
                                           style="width:100%; padding:10px 12px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); border-radius:10px; color:#fff; text-align:center;">
                                </form>
                            </td>
                            <td style="text-align:center;">
                                <button type="submit" form="form-{{ $selector->id }}" class="action-btn save-btn" title="Lưu thay đổi">
                                    <i class="fas fa-save"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination mt-4">
            {{ $selectors->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-code fa-4x mb-4" style="opacity: 0.4;"></i>
            <p>Không có site selector nào được cấu hình.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function closeToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 400);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            setTimeout(() => {
                closeToast();
            }, 4000);
        }
    });
</script>
@endpush