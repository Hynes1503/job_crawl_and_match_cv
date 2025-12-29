@extends('layouts.app')

@section('title', 'Quản lý CV - Job Matcher AI')

@push('styles')
    <style>
        .content-container {
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(12px);
            min-height: calc(100vh - 90px);
            padding: 20px 20px 60px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            border-radius: 16px;
            width: 100%;
            margin: 0 auto;
        }

        .crawl-panel {
            width: 100%;
            max-width: 1100px;
            padding: 0px 25px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .panel-title {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 6px;
        }

        .animated-text {
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff);
            background-size: 200% 200%;
            animation: textGradient 6s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .panel-desc {
            opacity: 0.85;
            text-align: center;
            max-width: 680px;
            margin-bottom: 40px;
            line-height: 1.6;
            font-size: 1.05rem;
        }

        /* Grid 3x4 */
        .cv-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            width: 100%;
        }

        .cv-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 480px;
            display: flex;
            flex-direction: column;
        }

        .cv-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 180, 255, 0.25);
        }

        .cv-preview {
            height: 360px;
            background: #111;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cv-preview embed {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #fff;
        }

        .cv-info {
            padding: 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .cv-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cv-meta {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .cv-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }

        .btn-view {
            background: rgba(0, 180, 255, 0.2);
            color: #00b4ff;
            border: 1px solid #00b4ff;
        }

        .btn-view:hover { background: #00b4ff; color: #fff; }

        .btn-delete {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        .btn-delete:hover { background: #ff6b6b; color: #fff; }

        /* Card Thêm CV */
        .add-card {
            background: rgba(0, 180, 255, 0.1);
            border: 2px dashed #00b4ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #00b4ff;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .add-card:hover {
            background: rgba(0, 180, 255, 0.2);
            transform: translateY(-10px);
        }

        .add-card i {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.8;
        }

        /* Card Xem thêm (Paginate) */
        .more-card {
            background: rgba(100, 100, 100, 0.2);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #aaa;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .more-card:hover {
            background: rgba(100, 100, 100, 0.3);
            color: #fff;
        }

        .more-card i {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        /* Thông báo thành công */
        .status-message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            z-index: 3000;
            opacity: 0;
            transition: opacity 0.5s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .status-message.show {
            opacity: 1;
        }

        .success {
            background: rgba(0, 255, 150, 0.2);
            border: 1px solid rgba(0, 255, 150, 0.4);
            color: #00ffaa;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: rgba(17, 17, 17, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .modal-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 20px;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 2rem;
            color: #aaa;
            cursor: pointer;
        }

        .close-modal:hover { color: #fff; }

        /* Responsive */
        @media (max-width: 992px) {
            .cv-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 576px) {
            .cv-grid { grid-template-columns: 1fr; }
            .cv-card { height: 520px; }
            .cv-preview { height: 380px; }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="crawl-panel">
            <h2 class="panel-title">
                <span class="animated-text">Quản Lý CV</span>
            </h2>
            <p class="panel-desc">Xem trước và quản lý CV để hệ thống tìm việc làm IT phù hợp nhất cho bạn.</p>

            <!-- Grid 3x4: 1 ô thêm + 10 CV + 1 ô xem thêm -->
            <div class="cv-grid">
                <!-- Ô 1: Thêm CV mới -->
                <div class="cv-card add-card" onclick="document.getElementById('uploadModal').classList.add('active')">
                    <i class="fa-solid fa-plus"></i>
                    <div>Thêm CV Mới</div>
                </div>

                <!-- Các CV (tối đa 10 cái hiển thị ở đây) -->
                @forelse($cvs->take(10) as $cv)
                    <div class="cv-card">
                        <div class="cv-preview">
                            @if(strtolower(pathinfo($cv->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                <embed src="{{ $cv->url }}#view=Fit&page=1&toolbar=0&navpanes=0" type="application/pdf">
                            @else
                                <div style="color: #888; font-size: 4rem;">
                                    <i class="fa-solid fa-file-word"></i>
                                </div>
                            @endif
                        </div>

                        <div class="cv-info">
                            <div class="cv-name" title="{{ $cv->original_name }}">
                                {{ $cv->original_name }}
                            </div>
                            <div class="cv-meta">
                                {{ $cv->created_at->format('d/m/Y H:i') }}
                            </div>

                            <div class="cv-actions">
                                <a href="{{ $cv->url }}" target="_blank" class="btn-sm btn-view">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                                <form action="{{ route('cv.destroy', $cv) }}" method="POST"
                                      onsubmit="return confirm('Xóa CV này vĩnh viễn?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-delete">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Nếu chưa có CV nào, hiển thị placeholder -->
                    @for($i = 1; $i <= 10; $i++)
                        <div class="cv-card" style="opacity: 0.3; pointer-events: none;">
                            <div class="cv-preview">
                                <i class="fa-solid fa-file-pdf" style="font-size: 5rem; color: #444;"></i>
                            </div>
                            <div class="cv-info">
                                <div class="cv-name">Chưa có CV</div>
                            </div>
                        </div>
                    @endfor
                @endforelse

                <!-- Ô cuối: Xem thêm / Phân trang -->
                <div class="cv-card more-card"
                     onclick="alert('Tính năng phân trang đang phát triển.\nBạn hiện có {{ $cvs->count() }} CV.')">
                    @if($cvs->count() > 10)
                        <i class="fa-solid fa-arrow-right"></i>
                        <div>Xem thêm<br><small>({{ $cvs->count() - 10 }} CV khác)</small></div>
                    @else
                        <i class="fa-solid fa-check"></i>
                        <div>Đã hiển thị<br>tất cả CV</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload CV -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="document.getElementById('uploadModal').classList.remove('active')">&times;</span>
            <h3 class="modal-title">Upload CV Mới</h3>
            <form action="{{ route('cv.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 20px;">
                    <input type="file" name="cv" accept=".pdf,.doc,.docx" required
                           style="width: 100%; padding: 12px; background: rgba(17,17,17,0.8); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; color: #fff;">
                    @error('cv')
                        <small style="color: #ff6b6b; display: block; margin-top: 8px;">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" style="width: 100%; padding: 14px; background: #00b4ff; color: #fff; border: none; border-radius: 999px; font-size: 1.1rem; font-weight: 700;">
                    <i class="fa-solid fa-upload"></i> Upload Ngay
                </button>
            </form>
        </div>
    </div>

    <!-- Thông báo thành công (tự ẩn sau 2s) -->
    @if(session('success'))
        <div id="successToast" class="status-message success show">
            {{ session('success') }}
        </div>
    @endif

    <script>
        // Tự ẩn thông báo sau 2 giây
        const toast = document.getElementById('successToast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }, 2000);
        }

        // Đóng modal khi click ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('uploadModal');
            if (event.target == modal) {
                modal.classList.remove('active');
            }
        }
    </script>
@endsection