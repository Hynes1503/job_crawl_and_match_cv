@extends('layouts.admin')

@section('page-title', 'Manage Users')

@section('content')
    <div class="container-fluid">
        <div class="header mb-5 text-center">
            <h1 style="font-size: 2.6rem; margin-bottom: 16px;">
                <span
                    style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
                     background-size: 200% 200%;
                     animation: textGradient 6s ease infinite;
                     -webkit-background-clip: text;
                     -webkit-text-fill-color: transparent;">
                    Quản Lý Người Dùng
                </span>
            </h1>
        </div>

        <!-- Form lọc -->
        <div class="mb-5">
            <form method="GET" action="{{ route('admin.users') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Tìm kiếm (tên hoặc email)</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Nhập tên hoặc email..."
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Vai trò</label>
                    <select name="role" class="form-select custom-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn w-100"
                        style="background: #00b4ff; border: none; padding: 10px; color: #000; font-weight: 600;">
                        <i class="fas fa-search me-2"></i>Lọc
                    </button>
                </div>

                <div class="col-md-2">
                    @if (request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary w-100"
                            style="border: 1px solid rgba(255,255,255,0.3); color: #bbd0ff; padding: 10px;">
                            <i class="fas fa-times me-2"></i>Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if ($users->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Người Dùng</th>
                            <th>Email</th>
                            <th>Vai Trò</th>
                            <th>Ngày Tạo</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $previousRole = null;
                        @endphp

                        @foreach ($users as $user)
                            @if ($previousRole !== null && $previousRole !== $user->role)
                                <!-- Đường ngăn cách gradient -->
                                <tr>
                                    <td colspan="6"
                                        style="padding: 0; height: 8px; background: linear-gradient(to right, transparent, #00b4ff, transparent); opacity: 0.6;">
                                    </td>
                                </tr>
                                <!-- Tiêu đề nhóm mới -->
                                <tr>
                                    <td colspan="6"
                                        style="text-align: center; padding: 16px; font-weight: 600; color: #00ffaa; font-size: 1.1rem; background: rgba(0, 255, 170, 0.08);">
                                        <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-users' }} me-2"></i>
                                        {{ $user->role == 'admin' ? 'QUẢN TRỊ VIÊN' : 'NGƯỜI DÙNG THƯỜNG' }}
                                    </td>
                                </tr>
                            @elseif($loop->first)
                                <!-- Tiêu đề nhóm đầu tiên -->
                                <tr>
                                    <td colspan="6"
                                        style="text-align: center; padding: 16px; font-weight: 600; color: #00ffaa; font-size: 1.1rem; background: rgba(0, 255, 170, 0.08);">
                                        <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-users' }} me-2"></i>
                                        {{ $user->role == 'admin' ? 'QUẢN TRỊ VIÊN' : 'NGƯỜI DÙNG THƯỜNG' }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td class="fw-bold">#{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #00b4ff, #00ffaa); color: #000; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-right: 12px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">{{ $user->name }}</div>
                                            <div style="font-size: 0.82rem; opacity: 0.7;">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="opacity: 0.9;">{{ $user->email }}</span>
                                </td>
                                <td>
                                    <span class="status {{ $user->role == 'admin' ? 'status-admin' : 'status-user' }}">
                                        <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-user' }}"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if (Auth::id() === $user->id)
                                        <span class="text-muted fst-italic">Current account</span>
                                    @else
                                        <form action="{{ route('admin.users.update', $user) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <div class="d-flex align-items-center gap-2">
                                                <select name="role" class="form-select form-select-sm custom-select-sm">
                                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>
                                                        User</option>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                        Admin</option>
                                                </select>
                                                <button type="submit" class="action-btn save-btn">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </td>

                            </tr>

                            @php $previousRole = $user->role; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="mt-5">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-users fa-4x mb-4" style="opacity: 0.4;"></i>
                <p>Không tìm thấy người dùng nào phù hợp với bộ lọc.</p>
            </div>
        @endif
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
            margin: 0;
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-admin {
            background: rgba(255, 100, 100, .2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 100, 100, 0.4);
        }

        .status-user {
            background: rgba(0, 180, 255, .2);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.4);
        }

        .action-btn {
            background: none;
            border: 1px solid #00ffaa;
            color: #00ffaa;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: rgba(0, 255, 150, .15);
            color: #00ffdd;
            transform: translateY(-2px);
        }

        .save-btn {
            border-color: #00ffaa;
            color: #00ffaa;
        }

        .save-btn:hover {
            background: rgba(0, 255, 150, .2);
        }

        /* FIX SELECT - Chữ rõ ràng, đẹp hơn */
        .custom-select,
        .custom-select-sm {
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            backdrop-filter: blur(4px);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .custom-select option,
        .custom-select-sm option {
            background: #1a1a2e !important;
            color: #ffffff !important;
        }

        .custom-select:focus,
        .custom-select-sm:focus {
            border-color: #00b4ff !important;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2) !important;
            outline: none;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbd0ff;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: rgba(0, 180, 255, 0.2);
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
            padding: 100px 20px;
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

        @keyframes fadeIn {
            from {
                opacity: 0;transform: translateY(10 px);
            }
            to {
                opacity: 1;transform: translateY(0);
            }
        }
    </script>
@endpush
