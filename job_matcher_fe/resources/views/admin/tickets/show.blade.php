@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="container-fluid">

    <div class="header mb-5 text-center">
        <h1 style="font-size: 2.6rem; margin-bottom: 12px;">
            <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #ff88ff);
                         background-size: 200% 200%;
                         animation: textGradient 8s ease infinite;
                         -webkit-background-clip: text;
                         -webkit-text-fill-color: transparent;">
                Ticket #{{ $ticket->id }}
            </span>
        </h1>
        <p class="stats opacity-85">
            {{ $ticket->title }}
        </p>
    </div>


    <div class="status-panel mb-5">
        <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
            @csrf
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <label class="form-label mb-0 fw-bold" style="font-size: 1.15rem; color: #00d4ff;">
                    Trạng thái hiện tại:
                </label>
                <select name="status" class="form-select w-auto" style="min-width: 180px;">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="processing" {{ $ticket->status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <button type="submit" class="btn btn-update">
                    <i class="fas fa-sync-alt"></i> Cập Nhật Trạng Thái
                </button>
            </div>
        </form>
    </div>


    @if($ticket->status === 'closed')
        <div class="alert alert-closed mb-5">
            <i class="fas fa-lock me-2"></i>
            Ticket này đã được đóng. Không thể gửi thêm tin nhắn.
            <br>
            <small>Nếu cần mở lại, hãy cập nhật trạng thái ở trên.</small>
        </div>
    @endif

    <div class="messages-panel mb-5">
        <div class="messages-header">
            <h3>Cuộc hội thoại hỗ trợ</h3>
        </div>
        <div class="messages-body" id="messagesBody">
            @forelse($messages as $m)
                <div class="message-item {{ $m->sender_type === 'admin' ? 'message-admin' : 'message-user' }}">
                    <div class="message-sender">
                        <strong>{{ ucfirst($m->sender_type) }}</strong>
                        <small class="opacity-70 ms-2">
                            {{ $m->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="message-content">
                        {{ $m->message }}
                    </div>
                </div>
            @empty
                <div class="no-messages">
                    <i class="fas fa-comment-slash fa-3x mb-3" style="opacity: 0.4;"></i>
                    <p>Chưa có tin nhắn nào trong ticket này.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($ticket->status !== 'closed')
        <div class="reply-panel">
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                @csrf
                <div class="form-group mb-4">
                    <label class="form-label">Trả lời ticket</label>
                    <textarea name="message" class="form-control" rows="5" required
                              placeholder="Nhập nội dung phản hồi..."></textarea>
                </div>
                <button type="submit" class="btn btn-reply">
                    <i class="fas fa-paper-plane"></i> Gửi Trả Lời
                </button>
            </form>
        </div>
    @else
        <div class="closed-notice text-center py-5">
            <i class="fas fa-lock fa-4x mb-3" style="opacity: 0.5; color: #ff6b6b;"></i>
            <p class="lead fw-bold">Ticket đã đóng - Không thể gửi thêm tin nhắn</p>
            <small class="opacity-75">Bạn có thể mở lại ticket bằng cách cập nhật trạng thái ở trên.</small>
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

    .header {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        padding: 32px;
        border: 1px solid rgba(0, 180, 255, 0.15);
    }

    .status-panel {
        background: rgba(15, 15, 25, 0.7);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .status-panel .form-label {
        font-weight: 700;
        color: #00d4ff;
    }

    .status-panel .form-select {
        background: rgba(30, 30, 45, 0.9);
        border: 1px solid rgba(0, 180, 255, 0.4);
        color: #ffffff;
        padding: 12px 16px;
        border-radius: 12px;
    }

    .status-panel .form-select:focus {
        border-color: #00b4ff;
        box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
    }

    .btn-update {
        background: linear-gradient(135deg, #ffdd00, #ffaa00);
        color: #000;
        font-weight: 700;
        padding: 12px 28px;
        border: none;
        border-radius: 12px;
        transition: all 0.4s ease;
    }

    .btn-update:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 200, 0, 0.4);
    }

    .alert-closed {
        background: rgba(255, 100, 100, 0.15);
        border: 1px solid rgba(255, 100, 100, 0.4);
        color: #ff6b6b;
        padding: 20px;
        border-radius: 16px;
        text-align: center;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(255, 100, 100, 0.1);
    }

    .closed-notice {
        background: rgba(255, 100, 100, 0.1);
        border: 1px solid rgba(255, 100, 100, 0.3);
        border-radius: 16px;
        padding: 60px 20px;
        text-align: center;
    }

    .closed-notice .lead {
        color: #ff6b6b;
        font-size: 1.4rem;
    }

    .messages-panel {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .messages-header {
        background: rgba(0, 180, 255, 0.08);
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .messages-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: #00d4ff;
        font-weight: 700;
    }

    .messages-body {
        max-height: 500px;
        overflow-y: auto;
        padding: 24px;
    }

    .message-item {
        margin-bottom: 24px;
        padding: 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .message-admin {
        background: rgba(0, 180, 255, 0.12);
        border-color: rgba(0, 180, 255, 0.3);
        margin-left: 20%;
    }

    .message-user {
        background: rgba(0, 255, 150, 0.08);
        border-color: rgba(0, 255, 150, 0.3);
        margin-right: 20%;
    }

    .message-sender {
        font-weight: 700;
        margin-bottom: 8px;
        color: #00ffaa;
    }

    .message-sender small {
        opacity: 0.7;
        font-weight: 400;
    }

    .message-content {
        line-height: 1.6;
        white-space: pre-wrap;
        color: #e0e0ff;
    }

    .no-messages {
        text-align: center;
        padding: 100px 20px;
        opacity: 0.7;
        font-size: 1.3rem;
    }

    .no-messages i {
        font-size: 4rem;
        margin-bottom: 20px;
        display: block;
        opacity: 0.4;
    }

    .reply-panel {
        background: rgba(15, 15, 25, 0.6);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .form-label {
        font-weight: 600;
        color: #00d4ff;
        margin-bottom: 12px;
        display: block;
    }

    .form-control {
        background: rgba(30, 30, 45, 0.9);
        border: 1px solid rgba(0, 180, 255, 0.4);
        color: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        transition: all 0.3s ease;
        font-size: 1rem;
        min-height: 140px;
    }

    .form-control:focus {
        border-color: #00ffaa;
        box-shadow: 0 0 0 4px rgba(0, 255, 150, 0.25);
        background: rgba(30, 30, 45, 1);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .btn-reply {
        background: linear-gradient(135deg, #00b4ff, #00ffaa);
        color: #000;
        font-weight: 700;
        padding: 14px 32px;
        border: none;
        border-radius: 12px;
        transition: all 0.4s ease;
    }

    .btn-reply:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 180, 255, 0.4);
    }

    @media (max-width: 768px) {
        .status-panel .d-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        .btn-update {
            margin-left: 0;
            margin-top: 16px;
        }
        .messages-body {
            max-height: 400px;
        }
    }
</style>
@endpush