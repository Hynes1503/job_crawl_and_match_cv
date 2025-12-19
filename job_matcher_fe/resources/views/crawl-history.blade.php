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
        }

        td {
            padding: 16px 12px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            font-size: 0.88rem;
            vertical-align: middle;
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

        /* Nút hành động */
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
        }

        .action-btn:hover {
            background: rgba(0, 180, 255, .15);
            color: #00d4ff;
        }

        .action-btn.match {
            border-color: #00ffaa;
            color: #00ffaa;
        }

        .action-btn.match:hover {
            background: rgba(0, 255, 150, .15);
            color: #00ffdd;
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

        /* Tab Kết quả Matching */
        .match-results {
            margin-top: 10px;
        }

        .match-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            border-left: 4px solid #00ffaa;
        }

        .match-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #00ffaa;
        }

        .match-score {
            font-size: 1.4rem;
            font-weight: 800;
            color: #00ffaa;
            margin: 8px 0;
        }

        .match-info {
            opacity: 0.9;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Tab JSON */
        .modal-body pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 0.88rem;
            line-height: 1.6;
            color: #e0e0e0;
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
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="header">
            <h1 style="font-size: 2.4rem; margin-bottom: 12px;">
                <span
                    style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Lịch Sử Crawl
                </span>
            </h1>
            <div class="stats">
                Tổng cộng có <strong>{{ number_format($jobsCount) }}</strong> công việc hiện tại trong hệ thống
            </div>
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
                            <th>Hành động</th>
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
                                    @if ($run->status == 'completed' && $run->detail && count($run->detail) > 0)
                                        <button class="action-btn" onclick="openModal({{ $run->id }}, 'json')">
                                            <i class="fas fa-code"></i> JSON
                                        </button>
                                        <button class="action-btn match" onclick="openModal({{ $run->id }}, 'match')">
                                            <i class="fas fa-search"></i> Tìm việc phù hợp
                                        </button>
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $crawlRuns->links() }}
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

    <!-- Modal chính -->
    <div class="overlay" id="overlay" onclick="closeModal()"></div>
    <div class="main-modal" id="mainModal">
        <div class="modal-header">
            <h3 id="modalTitle">Chi tiết Crawl</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="modal-tabs">
            <button class="tab-btn active" id="tab-json" onclick="switchTab('json')">Dữ liệu JSON</button>
            <button class="tab-btn" id="tab-match" onclick="switchTab('match')">Tìm việc phù hợp</button>
        </div>

        <div class="modal-body">
            <!-- Tab JSON -->
            <div id="tab-content-json">
                <pre id="jsonContent"></pre>
            </div>

            <!-- Tab Tìm việc phù hợp -->
            <div id="tab-content-match" style="display: none;">
                <div class="upload-form">
                    <form id="matchForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-area" onclick="document.getElementById('cvFile').click()">
                            <i class="fas fa-cloud-upload-alt"
                                style="font-size: 3rem; color: #00b4ff; margin-bottom: 16px;"></i>
                            <p style="margin: 0 0 16px; font-size: 1.1rem;">Kéo thả CV hoặc click để chọn file</p>
                            <p style="opacity: 0.7; font-size: 0.9rem;">Hỗ trợ: PDF, DOCX, TXT (tối đa 10MB)</p>
                            <input type="file" id="cvFile" name="cv_file" accept=".pdf,.docx,.txt" required>
                        </div>
                        <div style="margin-top: 20px;">
                            <input type="text" name="extra_skills" placeholder="Kỹ năng bổ sung (ví dụ: React, AWS)"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #444; background: rgba(255,255,255,0.05); color: #fff; margin-bottom: 12px;">
                            <input type="text" name="desired_position"
                                placeholder="Vị trí mong muốn (ví dụ: Senior Backend)"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #444; background: rgba(255,255,255,0.05); color: #fff;">
                        </div>
                        <button type="submit" class="upload-btn">Tìm việc phù hợp ngay</button>
                    </form>

                    <div id="matchResults" class="match-results" style="margin-top: 30px; display: none;"></div>
                    <div id="matchLoading" style="text-align: center; padding: 40px; display: none;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #00b4ff;"></i>
                        <p style="margin-top: 16px;">Đang phân tích CV và tìm việc phù hợp...</p>
                    </div>
                    <div id="matchError" style="color: #ff6b6b; text-align: center; display: none;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const crawlData = @json($crawlRuns->pluck('detail', 'id')->toArray());
        let currentRunId = null;

        // Route template với placeholder
        const matchRouteTemplate = "{{ route('match.with.run', ':id') }}";

        function openModal(runId, initialTab = 'json') {
            currentRunId = runId;
            const jobs = crawlData[runId] || [];

            document.getElementById('modalTitle').textContent = `Crawl #${runId} - ${jobs.length} công việc`;

            document.getElementById('jsonContent').textContent = JSON.stringify(jobs, null, 2);

            // Set form action đúng định dạng
            const form = document.getElementById('matchForm');
            form.action = matchRouteTemplate.replace(':id', runId);

            // Reset trạng thái
            form.reset();
            document.getElementById('matchResults').style.display = 'none';
            document.getElementById('matchResults').innerHTML = '';
            document.getElementById('matchLoading').style.display = 'none';
            document.getElementById('matchError').style.display = 'none';
            document.getElementById('matchError').innerHTML = '';

            switchTab(initialTab);

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
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');

            document.getElementById('tab-content-json').style.display = tab === 'json' ? 'block' : 'none';
            document.getElementById('tab-content-match').style.display = tab === 'match' ? 'block' : 'none';
        }

        // Xử lý submit form - ĐÃ SỬA ĐỂ KHÔNG BỊ LỖI JSON
        document.getElementById('matchForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            document.getElementById('matchLoading').style.display = 'block';
            document.getElementById('matchResults').style.display = 'none';
            document.getElementById('matchError').style.display = 'none';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // ← DÙNG TRỰC TIẾP TỪ BLADE, CHẮC CHẮN ĐÚNG
                    }
                });

                const text = await response.text();

                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    throw new Error(
                        'Lỗi server (có thể do phiên đăng nhập hết hạn). Vui lòng reload trang (F5) và thử lại.'
                        );
                }

                if (!response.ok) {
                    const errorMsg = data.message || data.error || 'Lỗi không xác định từ server';
                    throw new Error(errorMsg);
                }

                // Hiển thị kết quả thành công
                let html =
                    `<h3 style="text-align:center; color:#00ffaa; margin-bottom:20px;">Top 10 công việc phù hợp nhất</h3>`;
                if (data.match_results && data.match_results.length > 0) {
                    data.match_results.forEach(job => {
                        html += `
                <div class="match-item">
                    <div class="match-title">${job['Vị trí']}</div>
                    <div class="match-score">${job['Matching Score (%)']}%</div>
                    <div class="match-info">
                        <strong>Lương:</strong> ${job['Mức lương']}<br>
                        <strong>Kinh nghiệm:</strong> ${job['Kinh nghiệm']}<br>
                        <strong>Địa điểm:</strong> ${job['Địa điểm']}<br>
                        <strong>Kỹ năng phù hợp:</strong> ${job['Kỹ năng phù hợp']}<br>
                        <a href="${job.url}" target="_blank" style="color:#00b4ff;">Xem chi tiết việc làm →</a>
                    </div>
                </div>`;
                    });
                } else {
                    html +=
                        '<p style="text-align:center; opacity:0.8;">Không tìm thấy công việc phù hợp nào trong lần crawl này.</p>';
                }

                document.getElementById('matchResults').innerHTML = html;
                document.getElementById('matchResults').style.display = 'block';

            } catch (err) {
                document.getElementById('matchError').innerHTML = `<strong>Lỗi:</strong> ${err.message}`;
                document.getElementById('matchError').style.display = 'block';
            } finally {
                document.getElementById('matchLoading').style.display = 'none';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
@endpush
