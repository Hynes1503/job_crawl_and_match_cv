@extends('layouts.app')

@section('title', 'Lịch Sử Crawl - Job Matcher AI')

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
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 180, 255, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(0, 180, 255, 0.6);
            }
        }

        .filter-bar {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: #00d4ff;
            font-size: 0.95rem;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(0, 180, 255, 0.3);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 0.95rem;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
        }

        .filter-actions {
            width: 320px;
            margin: 12px auto 0;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .btn-filter {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 180, 255, 0.4);
        }

        .btn-reset {
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.4);
            color: #ff6b6b;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            background: rgba(255, 100, 100, 0.25);
        }

        .page-header {
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.1), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 180, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 32px;
            text-align: center;
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
        }

        .success-alert {
            background: linear-gradient(135deg, rgba(0, 255, 150, 0.15), rgba(0, 255, 150, 0.08));
            border: 1px solid rgba(0, 255, 150, 0.4);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: center;
            color: #00ffaa;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            animation: fadeIn 0.5s ease;
        }

        .success-alert i {
            font-size: 1.3rem;
        }

        .table-container {
            background: rgba(15, 15, 25, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 32px;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
            margin: 0;
        }

        .table-title i {
            font-size: 1.8rem;
            color: #00b4ff;
        }

        /* Modern Table */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .modern-table thead th {
            background: rgba(0, 0, 0, 0.3);
            padding: 14px 16px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
            border: none;
        }

        .modern-table tbody tr {
            background: rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(0, 180, 255, 0.08);
            transform: translateX(4px);
        }

        .modern-table tbody td {
            padding: 18px 16px;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
        }

        .modern-table tbody td:first-child {
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .modern-table tbody td:last-child {
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: rgba(0, 255, 150, 0.15);
            color: #00ffaa;
            border: 1px solid rgba(0, 255, 150, 0.3);
        }

        .badge-warning {
            background: rgba(255, 200, 0, 0.15);
            color: #ffdd00;
            border: 1px solid rgba(255, 200, 0, 0.3);
        }

        .badge-danger {
            background: rgba(255, 100, 100, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 100, 100, 0.3);
        }

        .action-btn {
            background: rgba(0, 180, 255, 0.1);
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #00b4ff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 4px;
        }

        .action-btn:hover {
            background: rgba(0, 180, 255, 0.2);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
            transform: translateY(-2px);
        }

        .action-btn.btn-success {
            background: rgba(0, 255, 150, 0.1);
            border-color: rgba(0, 255, 150, 0.3);
            color: #00ffaa;
        }

        .action-btn.btn-success:hover {
            background: rgba(0, 255, 150, 0.2);
            color: #00ffdd;
        }

        .action-btn.btn-danger {
            background: rgba(255, 100, 100, 0.1);
            border-color: rgba(255, 100, 100, 0.3);
            color: #ff6b6b;
        }

        .action-btn.btn-danger:hover {
            background: rgba(255, 100, 100, 0.2);
            color: #ff8888;
        }

        .action-btn.btn-export {
            background: rgba(0, 255, 133, 0.1);
            border-color: rgba(0, 255, 133, 0.3);
            color: #00ff85;
        }

        .action-btn.btn-export:hover {
            background: rgba(0, 255, 133, 0.2);
            color: #00ffaa;
        }

        .highlight-number {
            font-weight: 800;
            color: #00d4ff;
            font-size: 1.05rem;
        }

        .empty-state {
            text-align: center;
            padding: 100px 20px;
        }

        .empty-state i {
            font-size: 6rem;
            opacity: 0.15;
            margin-bottom: 24px;
            display: block;
            color: #00b4ff;
        }

        .empty-state h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 12px;
            opacity: 0.8;
        }

        .empty-state p {
            opacity: 0.6;
            margin-bottom: 32px;
            font-size: 1.1rem;
        }

        .cta-btn {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border: none;
            color: #000;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 255, 0.4);
            color: #000;
        }

        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 255, 0.2);
            color: #00b4ff;
            border-radius: 10px;
            padding: 10px 16px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .pagination .page-link:hover {
            background: rgba(0, 180, 255, 0.2);
            color: #00d4ff;
            border-color: rgba(0, 180, 255, 0.5);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            border-color: transparent;
            color: #000;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }

        .overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .main-modal {
            position: fixed;
            top: 0;
            right: -750px;
            width: 750px;
            height: 100vh;
            background: rgba(15, 15, 25, 0.98);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(0, 180, 255, 0.3);
            box-shadow: -20px 0 60px rgba(0, 0, 0, 0.9);
            z-index: 9100;
            transition: right 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
        }

        .main-modal.open {
            right: 0;
        }

        .modal-header {
            padding: 24px 32px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.3);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
        }

        .close-btn {
            background: rgba(255, 100, 100, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.3);
            color: #ff6b6b;
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 100, 100, 0.25);
            color: #ff8888;
            transform: rotate(90deg);
        }

        .modal-tabs {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .tab-btn {
            flex: 1;
            padding: 16px;
            background: none;
            border: none;
            color: #888;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .tab-btn:hover {
            color: #00b4ff;
            background: rgba(0, 180, 255, 0.05);
        }

        .tab-btn.active {
            color: #00d4ff;
            background: rgba(0, 180, 255, 0.1);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #00b4ff, #00ffaa);
        }

        .modal-body {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        #jsonContent {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(0, 180, 255, 0.2);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #00ffaa;
            overflow-x: auto;
        }

        .upload-form {
            max-width: 100%;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-weight: 600;
            color: #fff;
            font-size: 1rem;
        }

        .form-label i {
            color: #00b4ff;
        }

        .cv-select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(0, 180, 255, 0.3);
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .cv-select:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }

        .cv-select option {
            background: #1a1a2e;
            color: #fff;
            padding: 10px;
        }

        .upload-area {
            border: 2px dashed rgba(0, 180, 255, 0.4);
            border-radius: 16px;
            padding: 48px 24px;
            background: rgba(0, 180, 255, 0.05);
            transition: all 0.3s;
            cursor: pointer;
            text-align: center;
        }

        .upload-area:hover {
            background: rgba(0, 180, 255, 0.12);
            border-color: rgba(0, 180, 255, 0.6);
        }

        .upload-area input {
            display: none;
        }

        .upload-area i {
            font-size: 4rem;
            color: #00b4ff;
            margin-bottom: 20px;
            display: block;
        }

        .file-name-display {
            margin-top: 16px;
            padding: 12px;
            background: rgba(0, 255, 150, 0.1);
            border: 1px solid rgba(0, 255, 150, 0.3);
            border-radius: 10px;
            color: #00ffaa;
            font-weight: 600;
            word-break: break-all;
        }

        .form-input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }

        .form-input::placeholder {
            color: #888;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #00b4ff, #00ffaa);
            color: #000;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 24px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 255, 0.4);
        }

        #cv-preview {
            margin-bottom: 24px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            border: 1px solid rgba(0, 180, 255, 0.2);
        }

        #cv-preview h4 {
            color: #00b4ff;
            margin-bottom: 16px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .match-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 20px;
            border-left: 4px solid #00ffaa;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease forwards;
        }

        .match-item:hover {
            background: rgba(0, 180, 255, 0.08);
            transform: translateX(8px);
        }

        .match-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: #00ffaa;
            margin-bottom: 12px;
        }

        .match-details {
            margin: 12px 0;
            color: #aaa;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .match-details strong {
            color: #00b4ff;
        }

        .skill-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .skill-tag {
            background: rgba(0, 180, 255, 0.15);
            color: #00b4ff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid rgba(0, 180, 255, 0.3);
        }

        .skill-tag.missing {
            background: rgba(255, 80, 80, 0.18);
            color: #ff6b6b;
            border: 1px solid rgba(255, 80, 80, 0.45);
        }

        .missing-section {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed rgba(255, 100, 100, 0.25);
        }

        .missing-section h5 {
            color: #ff8888;
            font-size: 0.95rem;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .no-missing {
            color: #00ffaa;
            font-style: italic;
            opacity: 0.8;
            margin-top: 12px;
        }

        .skills-container {
            margin-top: 16px;
        }

        .score-container {
            text-align: center;
            margin-left: 24px;
        }

        .score-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: conic-gradient(#00ffaa calc(var(--percent) * 1%), rgba(255, 255, 255, 0.1) 0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto 12px;
        }

        .score-circle::before {
            content: attr(data-score);
            position: absolute;
            width: 72px;
            height: 72px;
            background: rgba(15, 15, 25, 0.98);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 800;
            color: #00ffaa;
        }

        .view-btn {
            margin-top: 16px;
            background: rgba(0, 180, 255, 0.15);
            border: 1px solid rgba(0, 180, 255, 0.3);
            color: #00b4ff;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .view-btn:hover {
            background: rgba(0, 180, 255, 0.25);
            color: #00d4ff;
            transform: translateY(-2px);
        }

        #matchLoading,
        #resultsLoading {
            text-align: center;
            padding: 60px 20px;
        }

        #matchLoading i,
        #resultsLoading i {
            font-size: 3rem;
            color: #00b4ff;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1200px) {
            .main-modal {
                width: 90%;
                right: -90%;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }

            .main-modal {
                width: 100%;
                right: -100%;
            }

            .modern-table {
                font-size: 0.85rem;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 12px 10px;
            }

            .action-btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }

            .score-circle {
                width: 70px;
                height: 70px;
            }

            .score-circle::before {
                width: 56px;
                height: 56px;
                font-size: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div id="successMessage" class="success-alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="page-header">
        <h1><i class="fa-solid fa-clipboard-list"></i> Lịch Sử Crawl</h1>
        <p>Quản lý và theo dõi tất cả các lần crawl việc làm của bạn</p>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('crawl.history') }}" class="filter-form">
            <div class="filter-group">
                <label for="keyword">Từ khóa</label>
                <input type="text" name="keyword" id="keyword" class="filter-input" value="{{ request('keyword') }}"
                    placeholder="VD: PHP, React, Laravel...">
            </div>

            <div class="filter-group">
                <label for="location">Địa điểm</label>
                <input type="text" name="location" id="location" class="filter-input" value="{{ request('location') }}"
                    placeholder="Hà Nội, TP.HCM, Remote...">
            </div>

            <div class="filter-group">
                <label for="level">Cấp bậc</label>
                <select name="level" id="level" class="filter-select">
                    <option value="">-- Tất cả --</option>
                    <option value="Intern" {{ request('level') === 'Intern' ? 'selected' : '' }}>Intern</option>
                    <option value="Fresher" {{ request('level') === 'Fresher' ? 'selected' : '' }}>Fresher</option>
                    <option value="Junior" {{ request('level') === 'Junior' ? 'selected' : '' }}>Junior</option>
                    <option value="Mid" {{ request('level') === 'Mid' ? 'selected' : '' }}>Mid/Senior</option>
                    <option value="Senior" {{ request('level') === 'Senior' ? 'selected' : '' }}>Senior</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="status">Trạng thái</label>
                <select name="status" id="status" class="filter-select">
                    <option value="">-- Tất cả --</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Thành công</option>
                    <option value="running" {{ request('status') === 'running' ? 'selected' : '' }}>Đang chạy</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="date_range">Thời gian</label>
                <select name="date_range" id="date_range" class="filter-select">
                    <option value="">-- Tất cả --</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="3months" {{ request('date_range') === '3months' ? 'selected' : '' }}>3 tháng qua
                    </option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Lọc
                </button>
                <a href="{{ route('crawl.history') }}" class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    @if ($crawlRuns->count() > 0)
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-history"></i>
                    <h2>Danh Sách Crawl</h2>
                </div>
            </div>

            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Thời Gian</th>
                        <th>Từ Khóa</th>
                        <th>Địa Điểm</th>
                        <th>Cấp Bậc</th>
                        <th>Số Việc</th>
                        <th>Trạng Thái</th>
                        <th>Matching</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($crawlRuns as $run)
                        <tr>
                            <td>
                                <i class="far fa-calendar-alt" style="margin-right: 8px; color: #00b4ff;"></i>
                                {{ $run->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td><strong>{{ $run->parameters['keyword'] ?? '-' }}</strong></td>
                            <td></td>
                            {{ $run->parameters['location'] ?? 'Tất cả' }}
                            </td>
                            <td>{{ $run->parameters['level'] ?? 'Tất cả' }}</td>
                            <td>
                                <span class="highlight-number">
                                    {{ $run->jobs_crawled ? number_format($run->jobs_crawled) : '0' }}
                                </span>
                            </td>
                            <td>
                                @if ($run->status == 'completed')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Thành công
                                    </span>
                                @elseif($run->status == 'running')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-spinner"></i> Đang chạy
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Thất bại
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($run->status == 'completed' && $run->detail && count($run->detail) > 0)
                                    <button class="action-btn" onclick="openModal({{ $run->id }}, 'json')">
                                        <i class="fas fa-code"></i> JSON
                                    </button>
                                    <button class="action-btn btn-success"
                                        onclick="openModal({{ $run->id }}, 'match')">
                                        <i class="fas fa-link"></i> Nối CV
                                    </button>
                                    @if (!empty($run->result) && count($run->result) > 0)
                                        <button class="action-btn btn-success"
                                            onclick="openModal({{ $run->id }}, 'results')">
                                            <i class="fas fa-trophy"></i> Kết quả
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('crawl-runs.destroy', $run->id) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa lần crawl này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-danger">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </form>

                                @if (
                                    $run->status == 'completed' &&
                                        $run->detail &&
                                        count($run->detail) > 0 &&
                                        !empty($run->result) &&
                                        count($run->result) > 0)
                                    <a href="{{ route('crawl-runs.export-training', $run->id) }}"
                                        class="action-btn btn-export" title="Export dữ liệu training">
                                        <i class="fas fa-file-export"></i> Export
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $crawlRuns->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Chưa Có Lịch Sử Crawl Hoặc Không Tìm Thấy Kết Quả</h3>
                <p>
                    @if (request()->hasAny(['keyword', 'location', 'level', 'status', 'min_jobs', 'date_range']))
                        Bộ lọc hiện tại không khớp với bất kỳ lần crawl nào.
                        <br>Hãy thử reset bộ lọc hoặc thay đổi điều kiện tìm kiếm.
                    @else
                        Bạn chưa thực hiện lần crawl nào. Hãy bắt đầu tìm kiếm việc làm ngay!
                    @endif
                </p>
                <a href="{{ route('crawl.form') ?? '/' }}" class="cta-btn">
                    Bắt Đầu Crawl
                </a>
            </div>
        </div>
    @endif

    <div class="overlay" id="overlay" onclick="closeModal()"></div>
    <div class="main-modal" id="mainModal">
        <div class="modal-header">
            <h3 id="modalTitle">Chi Tiết Crawl</h3>
            <button class="close-btn" onclick="closeModal()">×</button>
        </div>

        <div class="modal-tabs">
            <button class="tab-btn active" id="tab-json" onclick="switchTab('json')">
                <i class="fas fa-code"></i> Dữ liệu JSON
            </button>
            <button class="tab-btn" id="tab-match" onclick="switchTab('match')">
                <i class="fas fa-link"></i> Nối CV
            </button>
            <button class="tab-btn" id="tab-results" onclick="switchTab('results')">
                <i class="fas fa-trophy"></i> Kết quả Matching
            </button>
        </div>

        <div class="modal-body">
            <div id="tab-content-json">
                <pre id="jsonContent"></pre>
            </div>

            <div id="tab-content-match" style="display: none;">
                <div class="upload-form">
                    <form id="matchForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="existing_cv" class="form-label">
                                <i class="fas fa-file-alt"></i>
                                Chọn CV có sẵn
                            </label>
                            <select name="existing_cv" id="existing_cv" class="cv-select">
                                <option value="">-- Không chọn, upload CV mới --</option>
                                @foreach ($userCvs as $cv)
                                    <option value="{{ $cv->id }}" data-url="{{ $cv->url }}"
                                        data-mime="{{ $cv->mime_type }}">{{ $cv->original_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="cv-preview" style="display: none;">
                            <h4>
                                <i class="fas fa-eye"></i> Preview CV
                            </h4>
                            <div id="cv-preview-content"></div>
                        </div>

                        <div class="form-group">
                            <div class="upload-area" onclick="document.getElementById('cvFile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p style="margin: 0 0 12px; font-size: 1.2rem; font-weight: 600;">Kéo thả CV hoặc click để
                                    chọn</p>
                                <p style="opacity: 0.7; font-size: 0.95rem;">Hỗ trợ: PDF, DOCX, TXT (tối đa 10MB)</p>
                                <p style="opacity: 0.6; font-size: 0.85rem; margin-top: 8px;">CV mới upload sẽ được lưu vào
                                    thư viện</p>
                                <input type="file" id="cvFile" name="cv_file" accept=".pdf,.docx,.txt" required>
                            </div>
                            <div id="fileNameDisplay" class="file-name-display" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tools"></i> Kỹ năng bổ sung (tùy chọn)
                            </label>
                            <input type="text" name="extra_skills" class="form-input"
                                placeholder="VD: React, AWS, Docker...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-bullseye"></i> Vị trí mong muốn (tùy chọn)
                            </label>
                            <input type="text" name="desired_position" class="form-input"
                                placeholder="VD: Senior Backend Developer">
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-search"></i> Tìm Việc Phù Hợp Ngay
                        </button>
                    </form>

                    <div id="matchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p style="margin-top: 20px; font-size: 1.1rem;">Đang phân tích CV và đánh giá độ phù hợp...</p>
                    </div>
                </div>
            </div>

            <div id="tab-content-results" style="display: none;">
                <div id="resultsLoading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p style="margin-top: 20px; font-size: 1.1rem;">Đang tải kết quả matching...</p>
                </div>

                <div id="resultsContent">
                    <div id="noResultsMessage" style="text-align: center; padding: 80px 20px; opacity: 0.7;">
                        <i class="fas fa-inbox"
                            style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px; display: block;"></i>
                        <p style="font-size: 1.1rem;">Chưa có kết quả matching nào.</p>
                        <p style="opacity: 0.8;">Hãy upload CV ở tab "Nối CV" để bắt đầu.</p>
                    </div>

                    <div id="matchList" class="match-results">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const crawlData = @json($crawlData);
        let currentRunId = null;
        const matchRouteTemplate = "{{ route('match.with.run', ':id') }}";


        function openModal(runId, initialTab = 'json') {
            currentRunId = runId;
            const runData = crawlData[runId] || {};
            const jobs = runData.detail || [];
            const results = runData.result || [];

            document.getElementById('modalTitle').textContent =
                `Crawl #${runId} - ${jobs.length} công việc`;
            document.getElementById('jsonContent').textContent =
                JSON.stringify(jobs, null, 2);

            const form = document.getElementById('matchForm');
            form.action = matchRouteTemplate.replace(':id', runId);
            form.reset();

            document.getElementById('existing_cv').value = '';
            document.getElementById('existing_cv').dispatchEvent(new Event('change'));

            document.getElementById('fileNameDisplay').style.display = 'none';
            document.getElementById('fileNameDisplay').textContent = '';
            document.getElementById('matchLoading').style.display = 'none';

            if (Array.isArray(results) && results.length > 0) {
                renderMatchResults(results);
                switchTab('results');
            } else {
                document.getElementById('matchList').innerHTML = '';
                document.getElementById('noResultsMessage').style.display = 'block';
                switchTab(initialTab);
            }

            document.getElementById('overlay').classList.add('open');
            document.getElementById('mainModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('overlay').classList.remove('open');
            document.getElementById('mainModal').classList.remove('open');
            document.body.style.overflow = '';
            currentRunId = null;
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn =>
                btn.classList.remove('active')
            );
            document.getElementById(`tab-${tab}`).classList.add('active');

            document.getElementById('tab-content-json').style.display =
                tab === 'json' ? 'block' : 'none';
            document.getElementById('tab-content-match').style.display =
                tab === 'match' ? 'block' : 'none';
            document.getElementById('tab-content-results').style.display =
                tab === 'results' ? 'block' : 'none';
        }

        function renderMatchResults(results) {
            const container = document.getElementById('matchList');
            const noResultsMessage = document.getElementById('noResultsMessage');

            if (!Array.isArray(results) || results.length === 0) {
                container.innerHTML = '';
                noResultsMessage.style.display = 'block';
                return;
            }

            noResultsMessage.style.display = 'none';
            container.innerHTML = '';

            results.forEach((job, index) => {
                const score = parseFloat(job['Matching Score (%)']) || 0;

                const matchedSkillsStr = job['Kỹ năng phù hợp'] || '';
                const matchedSkills = matchedSkillsStr && matchedSkillsStr !== 'Không có' ?
                    matchedSkillsStr.split(',').map(s => s.trim()).filter(s => s) : [];

                const missingSkillsStr = job['Kỹ năng còn thiếu'] || '';
                const missingSkills = missingSkillsStr && missingSkillsStr !== 'Không thiếu kỹ năng nào' &&
                    missingSkillsStr !== 'Không có' ?
                    missingSkillsStr.split(',').map(s => s.trim()).filter(s => s) : [];

                const itemHTML = `
                    <div class="match-item" style="animation-delay: ${index * 0.1}s;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <div class="match-title">
                                    ${job['Vị trí'] || 'Không có tiêu đề'}
                                </div>
                                <div class="match-details">
                                    <strong>Lương:</strong> ${job['Mức lương'] || 'Thỏa thuận'}
                                    <br>
                                    <strong>Kinh nghiệm:</strong> ${job['Kinh nghiệm'] || 'Không yêu cầu'}
                                    <br>
                                    <strong>Địa điểm:</strong> ${job['Địa điểm'] || 'Không rõ'}
                                </div>
                                
                                ${matchedSkills.length > 0 ? `
                                         <div class="skills-container">
                                         <div style="margin-bottom: 8px; font-weight: 600; color: #00ffaa; font-size: 0.9rem;">
                                         <i class="fas fa-check-circle"></i> Kỹ năng phù hợp
                                         </div>
                                         <div class="skill-tags">
                                         ${matchedSkills.map(skill => 
                                         `<span class="skill-tag">${skill}</span>`
                                         ).join('')}
                                         </div>
                                         </div>
                                        ` : ''}
                                
                                ${missingSkills.length > 0 ? `
                                        <div class="missing-section">
                                        <h5>
                                        <i class="fas fa-exclamation-circle"></i> Kỹ năng còn thiếu
                                        </h5>
                                        <div class="skill-tags">
                                        ${missingSkills.map(skill => 
                                        `<span class="skill-tag missing">${skill}</span>`
                                        ).join('')}
                                        </div>
                                        </div>
                                        ` : `
                                        <div class="no-missing">
                                        <i class="fas fa-star"></i> Bạn đã đáp ứng đủ các kỹ năng yêu cầu!
                                        </div>
                                        `}
                            </div>
                            <div class="score-container">
                                <div class="score-circle" 
                                     data-score="${score.toFixed(1)}%" 
                                     style="--percent: ${score};"></div>
                                <div style="font-size: 0.85rem; opacity: 0.7;">Độ phù hợp</div>
                            </div>
                        </div>
                        <a href="${job.url || '#'}" target="_blank" class="view-btn">
                            <i class="fas fa-external-link-alt"></i> Xem chi tiết trên TopCV
                        </a>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', itemHTML);
            });
        }

        document.getElementById('cvFile').addEventListener('change', function() {
            const display = document.getElementById('fileNameDisplay');
            if (this.files && this.files.length > 0) {
                display.textContent = '📄 Đã chọn: ' + this.files[0].name;
                display.style.display = 'block';
            } else {
                display.style.display = 'none';
                display.textContent = '';
            }
        });

        document.getElementById('existing_cv').addEventListener('change', function() {
            const previewDiv = document.getElementById('cv-preview');
            const contentDiv = document.getElementById('cv-preview-content');
            const uploadArea = document.querySelector('.upload-area');
            const cvFile = document.getElementById('cvFile');

            if (this.value) {
                uploadArea.style.opacity = '0.5';
                uploadArea.style.pointerEvents = 'none';
                cvFile.required = false;
                cvFile.value = '';
                document.getElementById('fileNameDisplay').style.display = 'none';

                const selectedOption = this.options[this.selectedIndex];
                const fileName = selectedOption.text;
                const url = selectedOption.getAttribute('data-url');
                const mime = selectedOption.getAttribute('data-mime');
                const extension = fileName.split('.').pop().toLowerCase();

                contentDiv.innerHTML = `<p style="margin-bottom: 12px;"><strong>Tên file:</strong> ${fileName}</p>`;

                if (mime === 'application/pdf' || extension === 'pdf') {
                    contentDiv.innerHTML += `
                        <iframe src="${url}" width="100%" height="400px" 
                                style="border: 1px solid rgba(255,255,255,0.2); border-radius: 8px;"></iframe>
                        <p style="margin-top: 12px; font-size: 0.9rem; opacity: 0.8;">
                            <a href="${url}" target="_blank" style="color: #00ffaa; text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i> Mở trong tab mới
                            </a>
                        </p>
                    `;
                } else {
                    contentDiv.innerHTML += `
                        <p style="color: #888;">Loại file: ${extension.toUpperCase()}</p>
                        <a href="${url}" download style="color: #00ffaa; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-download"></i> Tải xuống để xem
                        </a>
                    `;
                }

                previewDiv.style.display = 'block';
            } else {
                uploadArea.style.opacity = '1';
                uploadArea.style.pointerEvents = 'auto';
                cvFile.required = true;
                previewDiv.style.display = 'none';
            }
        });

        document.getElementById('matchForm').addEventListener('submit', function() {
            document.getElementById('matchLoading').style.display = 'block';
        });


        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.opacity = '0';
                }, 3000);
                setTimeout(() => {
                    successMessage.remove();
                }, 3500);
            }
        });
    </script>
@endpush
