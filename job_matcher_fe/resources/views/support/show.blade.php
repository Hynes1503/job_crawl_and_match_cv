@extends('layouts.app')
@section('title', $ticket->title)

@push('styles')
    <style>
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
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Page Header */
        .ticket-header {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 32px;
            animation: fadeIn 0.6s ease;
        }

        .ticket-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ticket-title i {
            color: #00b4ff;
            font-size: 1.8rem;
        }

        .ticket-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .status-badge.open {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.3);
        }

        .status-badge.closed {
            background: rgba(100, 100, 100, 0.15);
            color: #888;
            border: 1px solid rgba(100, 100, 100, 0.3);
        }

        .status-badge.resolved {
            background: rgba(0, 255, 150, 0.15);
            color: #00ffaa;
            border: 1px solid rgba(0, 255, 150, 0.3);
        }

        .messages-container {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            animation: fadeIn 0.6s ease 0.2s backwards;
        }

        .messages-wrapper {
            height: 500px;
            overflow-y: auto;
            padding-right: 12px;
            margin-bottom: 24px;
        }

        .messages-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .messages-wrapper::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .messages-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #00b4ff, #00ffaa);
            border-radius: 10px;
        }

        .message {
            margin-bottom: 20px;
            display: flex;
            animation: fadeIn 0.4s ease;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.admin {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .message.user .message-content {
            align-items: flex-end;
        }

        .message.admin .message-content {
            align-items: flex-start;
        }

        .message-sender {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .message.user .message-sender {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            border: 1px solid rgba(0, 180, 255, 0.3);
            animation: slideInRight 0.4s ease;
        }

        .message.admin .message-sender {
            background: rgba(100, 100, 100, 0.15);
            color: #aaa;
            border: 1px solid rgba(100, 100, 100, 0.3);
            animation: slideIn 0.4s ease;
        }

        .message-bubble {
            padding: 16px 20px;
            border-radius: 16px;
            font-size: 1rem;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .message.user .message-bubble {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.2), rgba(0, 180, 255, 0.1));
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #fff;
            animation: slideInRight 0.4s ease;
        }

        .message.admin .message-bubble {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ddd;
            animation: slideIn 0.4s ease;
        }

        .message-time {
            font-size: 0.8rem;
            opacity: 0.6;
            margin-top: 4px;
        }

        .empty-messages {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-messages i {
            font-size: 5rem;
            color: #00b4ff;
            opacity: 0.2;
            margin-bottom: 20px;
        }

        .empty-messages p {
            font-size: 1.1rem;
            opacity: 0.7;
        }

        .reply-form {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-weight: 600;
            color: #fff;
        }

        .form-label i {
            color: #00b4ff;
        }

        .message-input {
            width: 100%;
            min-height: 120px;
            padding: 16px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s ease;
            margin-bottom: 16px;
        }

        .message-input:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(0, 0, 0, 0.5);
        }

        .message-input::placeholder {
            color: #888;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.05rem;
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

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #00b4ff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .back-btn:hover {
            background: rgba(0, 180, 255, 0.1);
            border-color: rgba(0, 180, 255, 0.3);
            transform: translateX(-4px);
            color: #00d4ff;
        }

        @media (max-width: 768px) {
            .ticket-header {
                padding: 24px;
            }

            .ticket-title {
                font-size: 1.6rem;
            }

            .messages-container {
                padding: 20px;
            }

            .messages-wrapper {
                height: 400px;
            }

            .message-content {
                max-width: 85%;
            }

            .message-bubble {
                padding: 12px 16px;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .ticket-header {
                padding: 20px;
            }

            .ticket-title {
                font-size: 1.4rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .message-content {
                max-width: 95%;
            }

            .messages-wrapper {
                height: 350px;
            }
        }
    </style>
@endpush

@section('content')
    <a href="{{ route('support.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Quay lại danh sách
    </a>

    <div class="ticket-header">
        <h1 class="ticket-title">
            <i class="fas fa-ticket-alt"></i>
            {{ $ticket->title }}
        </h1>
        <div class="ticket-meta">
            <span class="status-badge {{ strtolower($ticket->status) }}">
                <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                {{ ucfirst($ticket->status) }}
            </span>
            @if(isset($ticket->created_at))
                <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                    <i class="far fa-calendar-alt"></i>
                    Tạo lúc: {{ $ticket->created_at->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>
    </div>

    <div class="messages-container">
        <div class="messages-wrapper" id="messagesWrapper">
            @forelse($messages as $m)
                <div class="message {{ $m->sender_type == 'user' ? 'user' : 'admin' }}">
                    <div class="message-content">
                        <span class="message-sender">
                            <i class="fas fa-{{ $m->sender_type == 'user' ? 'user' : 'user-shield' }}"></i>
                            {{ $m->sender_type == 'user' ? 'Bạn' : 'Admin' }}
                        </span>
                        <div class="message-bubble">
                            {{ $m->message }}
                        </div>
                        @if(isset($m->created_at))
                            <span class="message-time">
                                {{ $m->created_at->format('H:i - d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-messages">
                    <i class="fas fa-comments"></i>
                    <p>Chưa có tin nhắn nào</p>
                </div>
            @endforelse
        </div>

        <div class="reply-form">
            <form method="POST" action="{{ route('support.reply', $ticket) }}" id="replyForm">
                @csrf
                <label class="form-label">
                    <i class="fas fa-pen"></i>
                    Tin nhắn của bạn
                </label>
                <textarea 
                    name="message" 
                    class="message-input" 
                    placeholder="Nhập nội dung tin nhắn..."
                    required
                    id="messageInput"></textarea>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    Gửi tin nhắn
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const messagesWrapper = document.getElementById('messagesWrapper');
        if (messagesWrapper) {
            messagesWrapper.scrollTop = messagesWrapper.scrollHeight;
        }

        const form = document.getElementById('replyForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageInput = document.getElementById('messageInput');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        });

        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 300) + 'px';
        });

        messageInput.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
    </script>
@endpush