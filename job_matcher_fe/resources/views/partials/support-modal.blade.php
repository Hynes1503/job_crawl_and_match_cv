<!-- ==================== SUPPORT MODAL ==================== -->
<style>
    .support-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(6px);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .support-modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .support-modal {
        background: rgba(20, 20, 35, 0.97);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(0, 180, 255, 0.35);
        border-radius: 16px;
        width: 100%;
        max-width: 540px;
        margin: 20px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.55);
        transform: translateY(30px) scale(0.94);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .support-modal.active {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 28px;
        border-bottom: 1px solid rgba(0, 180, 255, 0.18);
    }

    .modal-title {
        font-size: 1.45rem;
        font-weight: 700;
        background: linear-gradient(90deg, #00b4ff, #00ffaa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.8rem;
        color: rgba(255, 255, 255, 0.55);
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .modal-close:hover {
        color: #ff6b6b;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 28px;
    }

    .modal-body p {
        margin: 0 0 18px 0;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        background: rgba(30, 30, 45, 0.65);
        border: 1px solid rgba(0, 180, 255, 0.28);
        border-radius: 10px;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #00b4ff;
        box-shadow: 0 0 0 4px rgba(0, 180, 255, 0.22);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .is-invalid {
        border-color: #ff6b6b !important;
    }

    .invalid-feedback {
        color: #ff6b6b;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .modal-footer {
        padding: 20px 28px;
        border-top: 1px solid rgba(0, 180, 255, 0.18);
        display: flex;
        justify-content: flex-end;
        gap: 14px;
    }

    .btn {
        padding: 11px 22px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.98rem;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .btn-cancel {
        background: rgba(255, 255, 255, 0.09);
        color: white;
    }

    .btn-cancel:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .btn-submit {
        background: linear-gradient(90deg, #00b4ff, #0095d6);
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 180, 255, 0.4);
    }
</style>

<div class="support-modal-overlay" id="supportModal">
    <div class="support-modal" id="supportModalContent">
        <div class="modal-header">
            <h3 class="modal-title">Phản ánh / Báo lỗi</h3>
            <button class="modal-close" onclick="closeSupportModal()">×</button>
        </div>

        <form action="{{ route('support.store') }}" method="POST" id="supportForm">
            @csrf

            <div class="modal-body">
                <p>Vui lòng mô tả chi tiết vấn đề bạn gặp phải hoặc ý tưởng cải thiện hệ thống. Chúng tôi rất trân trọng
                    mọi phản hồi!</p>

                <div class="form-group">
                    <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        placeholder="Ví dụ: Lỗi đăng nhập / Gợi ý tính năng mới" value="{{ old('title') }}" required
                        maxlength="255">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category">Danh mục <span class="text-danger">*</span></label>
                    <select name="category" id="category" class="form-control @error('category') is-invalid @enderror"
                        required>
                        <option value="">-- Chọn danh mục --</option>
                        <option value="bug" {{ old('category') === 'bug' ? 'selected' : '' }}>Lỗi hệ thống (Bug)
                        </option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Tài khoản & Đăng
                            nhập</option>
                        <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Thanh toán & Gói
                            dịch vụ</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="priority">Mức độ ưu tiên <span class="text-danger">*</span></label>
                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror"
                        required>
                        <option value="">-- Chọn mức độ --</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="message">Nội dung chi tiết <span class="text-danger">*</span></label>
                    <textarea name="message" id="supportMessage" class="form-control @error('message') is-invalid @enderror" rows="6"
                        placeholder="Mô tả chi tiết vấn đề bạn gặp phải..." required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeSupportModal()">Hủy</button>
                <button type="submit" class="btn btn-submit">Gửi phản ánh</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSupportModal() {
        const overlay = document.getElementById('supportModal');
        const content = document.getElementById('supportModalContent');
        if (overlay && content) {
            overlay.classList.add('active');
            setTimeout(() => content.classList.add('active'), 30);
        }
    }

    function closeSupportModal() {
        const overlay = document.getElementById('supportModal');
        const content = document.getElementById('supportModalContent');
        if (overlay && content) {
            content.classList.remove('active');
            setTimeout(() => overlay.classList.remove('active'), 350);
        }
    }

    document.getElementById('supportModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSupportModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSupportModal();
    });
</script>
