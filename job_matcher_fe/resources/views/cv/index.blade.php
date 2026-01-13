@extends('layouts.app')

@section('title', 'Quản lý CV - Job Matcher AI')

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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }
        .page-header {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
            animation: fadeIn 0.6s ease;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(120deg, #00b4ff, #ffffff, #00ffaa);
            background-size: 200% 200%;
            animation: textGradient 8s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin: 0;
            line-height: 1.6;
        }

        .cv-container {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 40px;
            animation: fadeIn 0.6s ease 0.2s backwards;
        }

        .cv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
            width: 100%;
        }

        .cv-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 480px;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.5s ease;
        }

        .cv-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 180, 255, 0.3);
            border-color: rgba(0, 180, 255, 0.5);
        }

        .cv-preview {
            height: 340px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(17, 17, 17, 0.9));
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .cv-preview::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent, rgba(0, 0, 0, 0.3));
            pointer-events: none;
        }

        .cv-preview embed {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #fff;
        }

        .cv-preview .file-icon {
            font-size: 5rem;
            color: #00b4ff;
            opacity: 0.6;
        }
        .cv-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .cv-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cv-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cv-meta i {
            color: #00b4ff;
        }

        .cv-actions {
            margin-top: auto;
            display: flex;
            gap: 12px;
        }

        .btn-action {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-view {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.3);
        }

        .btn-view:hover {
            background: rgba(0, 180, 255, 0.3);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
        }

        .btn-delete {
            background: rgba(255, 100, 100, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 100, 100, 0.3);
        }

        .btn-delete:hover {
            background: rgba(255, 100, 100, 0.3);
            color: #ff8888;
            border-color: rgba(255, 100, 100, 0.5);
        }

        .add-card {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.08));
            border: 2px dashed rgba(0, 180, 255, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #00b4ff;
            font-size: 1.3rem;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .add-card:hover {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.2), rgba(0, 255, 170, 0.15));
            border-color: rgba(0, 180, 255, 0.6);
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 180, 255, 0.3);
        }

        .add-card i {
            font-size: 4.5rem;
            margin-bottom: 20px;
            opacity: 0.8;
            animation: pulse 2s ease-in-out infinite;
        }

        .more-card {
            background: rgba(100, 100, 100, 0.15);
            border: 2px dashed rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #888;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .more-card:hover {
            background: rgba(100, 100, 100, 0.25);
            color: #00b4ff;
            border-color: rgba(0, 180, 255, 0.4);
            transform: translateY(-12px);
        }

        .more-card i {
            font-size: 3.5rem;
            margin-bottom: 16px;
        }
        .placeholder-card {
            opacity: 0.25;
            pointer-events: none;
            background: rgba(0, 0, 0, 0.2);
        }

        .placeholder-card .cv-preview {
            background: rgba(0, 0, 0, 0.3);
        }

        .placeholder-card .file-icon {
            color: #444;
            opacity: 0.5;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(12px);
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: rgba(15, 15, 25, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 40px;
            position: relative;
            animation: slideIn 0.4s ease;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .modal-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(120deg, #00b4ff, #00ffaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .close-modal {
            width: 40px;
            height: 40px;
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.3);
            color: #ff6b6b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(255, 100, 100, 0.25);
            color: #ff8888;
            transform: rotate(90deg);
        }

        .upload-area {
            border: 2px dashed rgba(0, 180, 255, 0.4);
            border-radius: 16px;
            padding: 40px 24px;
            background: rgba(0, 180, 255, 0.05);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .upload-area:hover {
            background: rgba(0, 180, 255, 0.12);
            border-color: rgba(0, 180, 255, 0.6);
        }

        .upload-area i {
            font-size: 4rem;
            color: #00b4ff;
            margin-bottom: 16px;
            display: block;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .file-name-display {
            margin-top: 16px;
            padding: 12px;
            background: rgba(0, 255, 150, 0.1);
            border: 1px solid rgba(0, 255, 150, 0.3);
            border-radius: 10px;
            color: #00ffaa;
            font-weight: 600;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 255, 0.4);
        }
        .toast {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, rgba(0, 255, 150, 0.2), rgba(0, 255, 150, 0.1));
            border: 1px solid rgba(0, 255, 150, 0.4);
            color: #00ffaa;
            padding: 18px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.5s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast.show {
            opacity: 1;
            animation: fadeIn 0.5s ease;
        }

        .toast i {
            font-size: 1.3rem;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1 / -1;
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
        }

        @media (max-width: 1200px) {
            .cv-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 32px 24px;
            }

            .page-header h1 {
                font-size: 2.2rem;
            }

            .cv-container {
                padding: 24px;
            }

            .cv-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cv-card {
                height: 500px;
            }

            .cv-preview {
                height: 360px;
            }

            .modal-content {
                padding: 32px 24px;
            }

            .modal-title {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .cv-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1><i class="fa-solid fa-clipboard-list"></i> Quản Lý CV</h1>
        <p>Xem trước và quản lý CV để hệ thống tìm việc làm IT phù hợp nhất cho bạn</p>
    </div>

    <div class="cv-container">
        <div class="cv-grid">
            <div class="cv-card add-card" onclick="openUploadModal()">
                <i class="fas fa-plus-circle"></i>
                <div>Thêm CV Mới</div>
            </div>

            @forelse($cvs->take(10) as $cv)
                <div class="cv-card">
                    <div class="cv-preview">
                        @if (strtolower(pathinfo($cv->file_path, PATHINFO_EXTENSION)) === 'pdf')
                            <embed src="{{ $cv->url }}#view=Fit&page=1&toolbar=0&navpanes=0" type="application/pdf">
                        @else
                            <i class="fas fa-file-word file-icon"></i>
                        @endif
                    </div>

                    <div class="cv-info">
                        <div class="cv-name" title="{{ $cv->original_name }}">
                            {{ $cv->original_name }}
                        </div>
                        <div class="cv-meta">
                            <i class="far fa-calendar-alt"></i>
                            {{ $cv->created_at->format('d/m/Y H:i') }}
                        </div>

                        <div class="cv-actions">
                            <a href="{{ $cv->url }}" target="_blank" class="btn-action btn-view">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                            <form action="{{ route('cv.destroy', $cv) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa CV này vĩnh viễn?')" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" style="width: 100%;">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                @for ($i = 1; $i <= 11; $i++)
                    <div class="cv-card placeholder-card">
                        <div class="cv-preview">
                            <i class="fas fa-file-pdf file-icon"></i>
                        </div>
                        <div class="cv-info">
                            <div class="cv-name">Chưa có CV</div>
                            <div class="cv-meta">
                                <i class="far fa-calendar-alt"></i>
                                --/--/----
                            </div>
                        </div>
                    </div>
                @endfor
            @endforelse

            @if ($cvs->count() > 0)
                <div class="cv-card more-card" onclick="showMoreInfo()">
                    @if ($cvs->count() > 10)
                        <i class="fas fa-arrow-right"></i>
                        <div>Xem thêm<br><small>({{ $cvs->count() - 10 }} CV khác)</small></div>
                    @else
                        <i class="fas fa-check-circle"></i>
                        <div>Đã hiển thị<br>tất cả CV</div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Upload CV Mới</h3>
                <span class="close-modal" onclick="closeUploadModal()">×</span>
            </div>

            <form action="{{ route('cv.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="upload-area" onclick="document.getElementById('cvFileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p style="font-size: 1.1rem; font-weight: 600; margin: 0 0 8px 0;">
                        Kéo thả CV hoặc click để chọn
                    </p>
                    <p style="font-size: 0.9rem; opacity: 0.7; margin: 0;">
                        Hỗ trợ: PDF, DOC, DOCX (tối đa 10MB)
                    </p>
                    <input type="file" id="cvFileInput" name="cv" accept=".pdf,.doc,.docx" required>
                </div>

                <div id="fileNameDisplay" class="file-name-display" style="display: none;"></div>

                @error('cv')
                    <div style="color: #ff6b6b; font-size: 0.9rem; margin-bottom: 16px; text-align: center;">
                        {{ $message }}
                    </div>
                @enderror

                <button type="submit" class="submit-btn">
                    <i class="fas fa-upload"></i>
                    Upload Ngay
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div id="successToast" class="toast show">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('uploadModal');
            if (event.target == modal) {
                closeUploadModal();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUploadModal();
            }
        });

        document.getElementById('cvFileInput').addEventListener('change', function(e) {
            const display = document.getElementById('fileNameDisplay');
            if (this.files && this.files.length > 0) {
                display.textContent = '📄 Đã chọn: ' + this.files[0].name;
                display.style.display = 'block';
            } else {
                display.style.display = 'none';
            }
        });

        function showMoreInfo() {
            const count = {{ $cvs->count() }};
            if (count > 10) {
                alert(`Tính năng phân trang đang phát triển.\n\nBạn hiện có ${count} CV.\nChỉ hiển thị 10 CV đầu tiên.`);
            } else {
                alert(`Bạn đã xem tất cả ${count} CV.`);
            }
        }

        const toast = document.getElementById('successToast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    </script>
@endpush
