@extends('layouts.app')

@section('title', 'Lịch Sử Crawl - Job Matcher AI')

@push('styles')
    <style>
        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .stats {
            opacity: .85;
            font-size: 1rem;
            margin-bottom: 32px;
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
            padding: 16px 12px;
            text-align: left;
            font-size: 0.92rem;
            font-weight: 600;
            opacity: .9;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        td {
            padding: 16px 12px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            font-size: 0.88rem;
            vertical-align: middle;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-completed {
            background: rgba(0, 255, 150, .2);
            color: #00ffaa;
        }

        .status-running {
            background: rgba(0, 180, 255, .2);
            color: #00b4ff;
        }

        .status-failed {
            background: rgba(255, 100, 100, .2);
            color: #ff6b6b;
        }

        .message {
            max-width: 250px;
            word-break: break-word;
            opacity: .85;
            font-size: 0.85rem;
        }

        /* Nút hành động chung */
        .action-btn {
            background: none;
            border: 1px solid #00b4ff;
            color: #00b4ff;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 6px;
            text-decoration: none;
            display: inline-block;
        }

        .action-btn:hover {
            background: rgba(0, 180, 255, .15);
            color: #00d4ff;
        }

        /* Nút Matching (xanh lá) */
        .action-btn.match {
            border-color: #00ffaa;
            color: #00ffaa;
        }

        .action-btn.match:hover {
            background: rgba(0, 255, 150, .15);
            color: #00ffdd;
        }

        /* Nút Export Training Data (xanh lá đậm) */
        .action-btn.export-training {
            border-color: #00ff85;
            color: #00ff85;
            background: rgba(0, 255, 133, 0.08);
        }

        .action-btn.export-training:hover {
            background: rgba(0, 255, 133, 0.25);
            color: #00ffaa;
            transform: translateY(-1px);
        }

        /* Overlay & Modal chính */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
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
            right: -700px;
            width: 700px;
            height: 100vh;
            background: rgba(15, 15, 25, 0.98);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: -15px 0 50px rgba(0, 0, 0, 0.8);
            z-index: 9100;
            transition: right 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
        }

        .main-modal.open {
            right: 0;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.8rem;
            cursor: pointer;
            opacity: 0.7;
        }

        .close-btn:hover {
            opacity: 1;
        }

        .modal-tabs {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .tab-btn {
            flex: 1;
            padding: 14px;
            background: none;
            border: none;
            color: #aaa;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn.active {
            color: #00b4ff;
            border-bottom: 3px solid #00b4ff;
        }

        .modal-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        /* Tab Upload CV */
        .upload-form {
            text-align: center;
            padding: 40px 20px;
        }

        .upload-area {
            border: 2px dashed #00b4ff;
            border-radius: 12px;
            padding: 40px;
            background: rgba(0, 180, 255, 0.05);
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-area:hover {
            background: rgba(0, 180, 255, 0.1);
        }

        .upload-area input {
            display: none;
        }

        .file-name-display {
            margin-top: 16px;
            font-size: 1rem;
            color: #00ffaa;
            font-weight: 600;
            word-break: break-all;
        }

        .upload-btn {
            background: #00b4ff;
            color: #000;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        .cv-select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #444;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .cv-select:focus {
            outline: none;
            border-color: #00b4ff;
            box-shadow: 0 0 0 2px rgba(0, 180, 255, 0.2);
        }

        .cv-select option {
            background: #333;
            color: #fff;
        }

        .match-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            border-left: 4px solid #00ffaa;
        }

        .match-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #00ffaa;
            margin-bottom: 12px;
        }

        .match-details {
            margin: 8px 0 12px 0;
            color: #aaa;
            font-size: 0.9rem;
        }

        .skill-tags {
            margin-bottom: 16px;
        }

        .score-container {
            margin-left: 20px;
            text-align: center;
            margin-bottom: 16px;
        }

        /* Progress circle cho Matching Score */
        .score-circle {
            width: 80px;
            height: 80px;
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
            width: 64px;
            height: 64px;
            background: rgba(15, 15, 25, 0.98);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 800;
            color: #00ffaa;
        }

        /* Tag kỹ năng */
        .skill-tags {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .skill-tag {
            background: rgba(0, 180, 255, 0.2);
            color: #00b4ff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(0, 180, 255, 0.4);
        }

        /* Nút xem chi tiết */
        .view-btn {
            margin-top: 16px;
            background: #00b4ff;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s;
        }

        .view-btn:hover {
            background: #00d4ff;
            transform: translateY(-2px);
        }

        .no-data {
            text-align: center;
            padding: 80px 20px;
            opacity: .7;
            font-size: 1.2rem;
        }

        .pagination {
            margin-top: 30px;
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

        @media (max-width: 992px) {
            .main-modal {
                width: 100%;
                right: -100%;
            }

            .main-modal.open {
                right: 0;
            }

            .message {
                max-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .match-item {
                padding: 14px;
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

            .score-container {
                margin-left: 15px;
                margin-bottom: 12px;
            }

            .match-title {
                font-size: 1rem;
            }

            .match-details {
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div id="successMessage"
            style="background: rgba(0, 255, 150, 0.15);
                border: 1px solid #00ffaa;
                color: #00ffaa;
                padding: 16px;
                border-radius: 12px;
                margin-bottom: 24px;
                text-align: center;
                font-weight: 600;
                opacity: 1;
                transition: opacity 1s ease-out;">
            {{ session('success') }}
        </div>
    @endif
    <div class="container">
        <div class="header">
            <h1 style="font-size: 2.4rem; margin-bottom: 12px;">
                <span
                    style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Lịch Sử Crawl
                </span>
            </h1>
        </div>

        @if ($crawlRuns->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Từ khóa</th>
                            <th>Địa điểm</th>
                            <th>Cấp bậc</th>
                            <th>Mức lương</th>
                            <th>Yêu cầu</th>
                            <th>Crawl được</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Matching</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($crawlRuns as $run)
                            <tr>
                                <td>{{ $run->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $run->parameters['keyword'] ?? '-' }}</td>
                                <td>{{ $run->parameters['location'] ?? 'Tất cả' }}</td>
                                <td>{{ $run->parameters['level'] ?? 'Tất cả' }}</td>
                                <td>{{ $run->parameters['salary'] ?? 'Tất cả' }}</td>
                                <td>{{ $run->parameters['search_range'] ?? '-' }}</td>
                                <td><strong>{{ $run->jobs_crawled ? number_format($run->jobs_crawled) : '-' }}</strong></td>
                                <td>
                                    <span class="status status-{{ $run->status }}">
                                        {{ $run->status == 'completed' ? 'Thành công' : ($run->status == 'running' ? 'Đang chạy' : 'Thất bại') }}
                                    </span>
                                </td>
                                <td class="message">
                                    @if ($run->status == 'failed' && $run->error_message)
                                        <span style="color: #ff6b6b;">{{ Str::limit($run->error_message, 80) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{-- CỘT MATCHING: Chỉ các nút matching --}}
                                    @if ($run->status == 'completed' && $run->detail && count($run->detail) > 0)
                                        <button class="action-btn" onclick="openModal({{ $run->id }}, 'json')">
                                            <i class="fas fa-code"></i> JSON
                                        </button>
                                        <button class="action-btn match" onclick="openModal({{ $run->id }}, 'match')">
                                            <i class="fas fa-search"></i> Nối CV
                                        </button>

                                        @if (!empty($run->result) && count($run->result) > 0)
                                            <button class="action-btn" style="border-color: #00ffaa; color: #00ffaa;"
                                                onclick="openModal({{ $run->id }}, 'results')">
                                                <i class="fas fa-trophy"></i> Kết quả
                                            </button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{-- CỘT THAO TÁC: Xóa + Export Training --}}
                                    {{-- Nút Xóa --}}
                                    <form action="{{ route('crawl-runs.destroy', $run->id) }}" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa lần crawl này?\nDữ liệu JSON và kết quả matching sẽ bị xóa vĩnh viễn.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn"
                                            style="border-color: #ff6b6b; color: #ff6b6b;">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </form>

                                    {{-- Nút Export Training: Chỉ hiện khi completed + có detail + có ít nhất 1 kết quả matching --}}
                                    @if (
                                        $run->status == 'completed' &&
                                            $run->detail &&
                                            count($run->detail) > 0 &&
                                            !empty($run->result) &&
                                            count($run->result) > 0)
                                        <a href="{{ route('crawl-runs.export-training', $run->id) }}"
                                            class="action-btn export-training"
                                            title="Export dữ liệu crawl + CV text để training model AI">
                                            <i class="fas fa-file-export"></i> Export Training
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $crawlRuns->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-data">
                <p>Bạn chưa thực hiện lần crawl nào.</p>
                <a href="{{ route('dashboard') ?? '/' }}"
                    style="display: inline-flex; align-items: center; gap: 8px; color: #00b4ff; text-decoration: none; font-weight: 600; margin-top: 20px;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại dashboard
                </a>
            </div>
        @endif
    </div>

    <!-- Modal chính (giữ nguyên như cũ) -->
    <div class="overlay" id="overlay" onclick="closeModal()"></div>
    <div class="main-modal" id="mainModal">
        <div class="modal-header">
            <h3 id="modalTitle">Chi tiết Crawl</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="modal-tabs">
            <button class="tab-btn active" id="tab-json" onclick="switchTab('json')">Dữ liệu JSON</button>
            <button class="tab-btn" id="tab-match" onclick="switchTab('match')">Nối CV</button>
            <button class="tab-btn" id="tab-results" onclick="switchTab('results')">Kết quả Matching</button>
        </div>

        <div class="modal-body">
            <!-- Tab JSON -->
            <div id="tab-content-json">
                <pre id="jsonContent"></pre>
            </div>

            <!-- Tab Nối CV -->
            <div id="tab-content-match" style="display: none;">
                <div class="upload-form">
                    <form id="matchForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="margin-bottom: 20px;">
                            <label for="existing_cv"
                                style="display: block; margin-bottom: 8px; font-weight: 600; color: #fff;">
                                <i class="fas fa-file-alt" style="margin-right: 8px; color: #00b4ff;"></i>
                                Chọn CV có sẵn (tùy chọn)
                            </label>
                            <select name="existing_cv" id="existing_cv" class="cv-select">
                                <option value="">-- Không chọn, upload CV mới --</option>
                                @foreach ($userCvs as $cv)
                                    <option value="{{ $cv->id }}" data-url="{{ $cv->url }}"
                                        data-mime="{{ $cv->mime_type }}">{{ $cv->original_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- CV Preview Area -->
                        <div id="cv-preview"
                            style="display: none; margin-bottom: 20px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                            <h4 style="color: #00b4ff; margin-bottom: 12px; font-size: 1rem;">
                                <i class="fas fa-eye" style="margin-right: 8px;"></i>Preview CV
                            </h4>
                            <div id="cv-preview-content"></div>
                        </div>

                        <div class="upload-area" onclick="document.getElementById('cvFile').click()">
                            <i class="fas fa-cloud-upload-alt"
                                style="font-size: 3rem; color: #00b4ff; margin-bottom: 16px;"></i>
                            <p style="margin: 0 0 16px; font-size: 1.1rem;">Kéo thả CV hoặc click để chọn file</p>
                            <p style="opacity: 0.7; font-size: 0.9rem;">Hỗ trợ: PDF, DOCX, TXT (tối đa 10MB)</p>
                            <input type="file" id="cvFile" name="cv_file" accept=".pdf,.docx,.txt" required>
                        </div>
                        <div id="fileNameDisplay" class="file-name-display" style="display: none;"></div>

                        <div style="margin-top: 20px;">
                            <input type="text" name="extra_skills" placeholder="Kỹ năng bổ sung (ví dụ: React, AWS)"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #444; background: rgba(255,255,255,0.05); color: #fff; margin-bottom: 12px;">
                            <input type="text" name="desired_position"
                                placeholder="Vị trí mong muốn (ví dụ: Senior Backend)"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #444; background: rgba(255,255,255,0.05); color: #fff;">
                        </div>

                        <button type="submit" class="upload-btn">Tìm việc phù hợp ngay</button>
                    </form>

                    <div id="matchLoading" style="text-align: center; padding: 40px; display: none;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #00b4ff;"></i>
                        <p style="margin-top: 16px;">Đang phân tích CV và đánh giá...</p>
                    </div>
                </div>
            </div>

            <!-- Tab Kết quả Matching -->
            <div id="tab-content-results" style="display: none;">
                <div id="resultsLoading" style="text-align: center; padding: 60px; display: none;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #00b4ff;"></i>
                    <p style="margin-top: 16px;">Đang tải kết quả matching...</p>
                </div>

                <div id="resultsContent">
                    <div id="noResultsMessage" style="text-align: center; padding: 60px 20px; opacity: 0.7;">
                        <p>Chưa có kết quả matching nào.<br>Hãy upload CV ở tab "Nối CV" để bắt đầu.</p>
                    </div>

                    <div id="matchList" class="match-results">
                        <!-- Các job sẽ được render bằng JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ===============================
                                       DATA TỪ BACKEND
                                    =============================== */

        const crawlData = @json($crawlData);

        let currentRunId = null;
        const matchRouteTemplate = "{{ route('match.with.run', ':id') }}";

        /* ===============================
           MODAL CONTROL
        =============================== */

        function openModal(runId, initialTab = 'json') {
            currentRunId = runId;

            const runData = crawlData[runId] || {};
            const jobs = runData.detail || [];
            const results = runData.result || [];

            // Title
            document.getElementById('modalTitle').textContent =
                `Crawl #${runId} - ${jobs.length} công việc`;

            // JSON tab
            document.getElementById('jsonContent').textContent =
                JSON.stringify(jobs, null, 2);

            // Form action
            const form = document.getElementById('matchForm');
            form.action = matchRouteTemplate.replace(':id', runId);
            form.reset();

            // Reset existing_cv select
            document.getElementById('existing_cv').value = '';
            document.getElementById('existing_cv').dispatchEvent(new Event('change'));

            // Reset UI
            document.getElementById('fileNameDisplay').style.display = 'none';
            document.getElementById('fileNameDisplay').textContent = '';
            document.getElementById('matchLoading').style.display = 'none';

            // RENDER RESULT TỪ DB
            if (Array.isArray(results) && results.length > 0) {
                renderMatchResults(results);
                switchTab('results');
            } else {
                document.getElementById('matchList').innerHTML = '';
                document.getElementById('noResultsMessage').style.display = 'block';
                switchTab(initialTab);
            }

            // Open modal
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

        /* ===============================
           RENDER MATCH RESULTS
        =============================== */

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

                const skills = typeof job['Kỹ năng phù hợp'] === 'string' ?
                    job['Kỹ năng phù hợp'].split(',').map(s => s.trim()) : [];

                const itemHTML = `
                <div class="match-item"
                     style="animation: fadeIn 0.5s ease forwards;
                            animation-delay: ${index * 0.1}s;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div style="flex:1;">
                            <div class="match-title">
                                ${job['Vị trí'] || 'Không có tiêu đề'}
                            </div>

                            <div class="match-details">
                                <strong>Lương:</strong> ${job['Mức lương'] || 'Thỏa thuận'}
                                • <strong>Kinh nghiệm:</strong> ${job['Kinh nghiệm'] || 'Không yêu cầu'}
                                • <strong>Địa điểm:</strong> ${job['Địa điểm'] || 'Không rõ'}
                            </div>

                            ${skills.length > 0 ? `
                                    <div class="skill-tags">
                                        ${skills.map(skill =>
                                            `<span class="skill-tag">${skill}</span>`
                                        ).join('')}
                                    </div>
                                ` : ''}
                        </div>

                        <div class="score-container">
                            <div class="score-circle"
                                 data-score="${score.toFixed(1)}%"
                                 style="--percent:${score};"></div>
                            <div style="font-size:0.8rem;opacity:0.7;">
                                Độ phù hợp
                            </div>
                        </div>
                    </div>

                    <a href="${job.url || '#'}"
                       target="_blank"
                       class="view-btn">
                        Xem chi tiết trên TopCV ↗
                    </a>
                </div>
            `;

                container.insertAdjacentHTML('beforeend', itemHTML);
            });
        }

        /* ===============================
           FORM & UX
        =============================== */

        document.getElementById('cvFile').addEventListener('change', function() {
            const display = document.getElementById('fileNameDisplay');
            if (this.files && this.files.length > 0) {
                display.textContent = 'Đã chọn: ' + this.files[0].name;
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
                        <iframe src="${url}" width="100%" height="400px" style="border: 1px solid rgba(255,255,255,0.2); border-radius: 4px;"></iframe>
                        <p style="margin-top: 8px; font-size: 0.9rem; opacity: 0.8;">Preview PDF - <a href="${url}" target="_blank" style="color: #00ffaa;">Mở trong tab mới</a></p>
                    `;
                } else {
                    contentDiv.innerHTML += `
                        <p>Loại file: ${extension.toUpperCase()}</p>
                        <a href="${url}" download style="color: #00ffaa; text-decoration: none;">
                            <i class="fas fa-download" style="margin-right: 8px;"></i>Tải xuống để xem
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
                }, 2000);

                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
            }
        });
    </script>
@endpush
