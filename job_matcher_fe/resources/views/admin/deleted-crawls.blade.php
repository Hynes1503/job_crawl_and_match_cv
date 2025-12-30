@extends('layouts.admin')

@section('title', 'Các Lần Crawl Đã Xóa - Job Matcher AI')

@push('styles')
    <style>
        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 20px;
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

        .status-completed { background: rgba(0, 255, 150, .2); color: #00ffaa; }
        .status-running   { background: rgba(0, 180, 255, .2); color: #00b4ff; }
        .status-failed    { background: rgba(255, 100, 100, .2); color: #ff6b6b; }

        .message {
            max-width: 250px;
            word-break: break-word;
            opacity: .85;
            font-size: 0.85rem;
        }

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

        .action-btn.match {
            border-color: #00ffaa;
            color: #00ffaa;
        }

        .action-btn.match:hover {
            background: rgba(0, 255, 150, .15);
            color: #00ffdd;
        }

        .action-btn.results {
            border-color: #00ffaa;
            color: #00ffaa;
        }

        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        .no-data {
            text-align: center;
            padding: 80px 20px;
            opacity: .7;
            font-size: 1.2rem;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Modal styles (giống hệt trang lịch sử) */
        .overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.8); z-index: 9000;
            opacity: 0; visibility: hidden; transition: all 0.4s ease;
        }
        .overlay.open { opacity: 1; visibility: visible; }

        .main-modal {
            position: fixed; top: 0; right: -700px; width: 700px; height: 100vh;
            background: rgba(15, 15, 25, 0.98); backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255,255,255,0.1);
            box-shadow: -15px 0 50px rgba(0,0,0,0.8); z-index: 9100;
            transition: right 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex; flex-direction: column;
        }
        .main-modal.open { right: 0; }

        .modal-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
        .modal-header h3 { margin: 0; font-size: 1.4rem; font-weight: 700; }
        .close-btn { background: none; border: none; color: #fff; font-size: 1.8rem; cursor: pointer; opacity: 0.7; }
        .close-btn:hover { opacity: 1; }

        .modal-tabs { display: flex; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
        .tab-btn { flex: 1; padding: 14px; background: none; border: none; color: #aaa; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .tab-btn.active { color: #00b4ff; border-bottom: 3px solid #00b4ff; }

        .modal-body { flex: 1; padding: 20px; overflow-y: auto; }

        pre { white-space: pre-wrap; word-wrap: break-word; background: rgba(0,0,0,0.4); padding: 16px; border-radius: 8px; }

        @media (max-width: 992px) {
            .main-modal { width: 100%; right: -100%; }
            .main-modal.open { right: 0; }
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div id="successMessage"
            style="background: rgba(0, 255, 150, 0.15); border: 1px solid #00ffaa; color: #00ffaa; padding: 16px; border-radius: 12px; margin-bottom: 24px; text-align: center; font-weight: 600; opacity: 1; transition: opacity 1s ease-out;">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <div class="header">
            <h1 style="font-size: 2.4rem; margin-bottom: 12px;">
                <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Các Lần Crawl Đã Xóa
                </span>
            </h1>
            <div class="stats">
                Hiển thị các lần crawl đã bị xóa khỏi hệ thống (chỉ dành cho quản trị viên)
            </div>
        </div>

        @if ($deletedCrawls->count() > 0)
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
                            <th>Dữ liệu JSON</th>
                            <th>Kết quả Matching</th>
                            <th>Xóa bởi</th>
                            <th>Thời gian xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deletedCrawls as $crawl)
                            <tr>
                                <td>{{ $crawl->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $crawl->parameters['keyword'] ?? '-' }}</td>
                                <td>{{ $crawl->parameters['location'] ?? 'Tất cả' }}</td>
                                <td>{{ $crawl->parameters['level'] ?? 'Tất cả' }}</td>
                                <td>{{ $crawl->parameters['salary'] ?? 'Tất cả' }}</td>
                                <td>{{ $crawl->parameters['search_range'] ?? '-' }}</td>
                                <td><strong>{{ $crawl->jobs_crawled ? number_format($crawl->jobs_crawled) : '-' }}</strong></td>
                                <td>
                                    <span class="status status-{{ $crawl->status }}">
                                        {{ $crawl->status == 'completed' ? 'Thành công' : ($crawl->status == 'running' ? 'Đang chạy' : 'Thất bại') }}
                                    </span>
                                </td>
                                <td class="message">
                                    @if ($crawl->status == 'failed' && $crawl->error_message)
                                        <span style="color: #ff6b6b;">{{ Str::limit($crawl->error_message, 80) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($crawl->status == 'completed' && is_array($crawl->detail) && count($crawl->detail) > 0)
                                        <button class="action-btn" onclick="openModal({{ $crawl->id }}, 'json')">
                                            JSON ({{ count($crawl->detail) }})
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if (is_array($crawl->result) && count($crawl->result) > 0)
                                        <button class="action-btn results match" onclick="openModal({{ $crawl->id }}, 'results')">
                                            Kết quả ({{ count($crawl->result) }})
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $crawl->deletedBy ? $crawl->deletedBy->name : 'N/A' }}</td>
                                <td>{{ $crawl->deleted_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $deletedCrawls->links() }}
            </div>
        @else
            <div class="no-data">
                <p>Không có lần crawl nào đã bị xóa.</p>
            </div>
        @endif
    </div>

    <!-- Modal xem chi tiết (giống trang lịch sử) -->
    <div class="overlay" id="overlay" onclick="closeModal()"></div>
    <div class="main-modal" id="mainModal">
        <div class="modal-header">
            <h3 id="modalTitle">Chi tiết Crawl Đã Xóa</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="modal-tabs">
            <button class="tab-btn active" id="tab-json" onclick="switchTab('json')">Dữ liệu JSON</button>
            <button class="tab-btn" id="tab-results" onclick="switchTab('results')">Kết quả Matching</button>
        </div>

        <div class="modal-body">
            <div id="tab-content-json">
                <pre id="jsonContent"></pre>
            </div>

            <div id="tab-content-results" style="display: none;">
                <div id="resultsContent">
                    <div id="matchList"></div>
                    <div id="noResultsMessage" style="text-align: center; padding: 60px 20px; opacity: 0.7; display: none;">
                        Không có kết quả matching nào được lưu.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const crawlData = @json($deletedCrawls->keyBy('id')->map(fn($c) => ['detail' => $c->detail, 'result' => $c->result]));

        let currentRunId = null;

        function openModal(runId, initialTab = 'json') {
            currentRunId = runId;
            const runData = crawlData[runId] || { detail: [], result: [] };
            const jobs = runData.detail || [];
            const results = runData.result || [];

            document.getElementById('modalTitle').textContent = `Crawl Đã Xóa #${runId} - ${jobs.length} công việc`;

            // Tab JSON
            document.getElementById('jsonContent').textContent = JSON.stringify(jobs, null, 2);

            // Tab Kết quả
            if (results.length > 0) {
                renderMatchResults(results);
                document.getElementById('noResultsMessage').style.display = 'none';
            } else {
                document.getElementById('matchList').innerHTML = '';
                document.getElementById('noResultsMessage').style.display = 'block';
            }

            switchTab(initialTab);

            document.getElementById('overlay').classList.add('open');
            document.getElementById('mainModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('overlay').classList.remove('open');
            document.getElementById('mainModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');

            document.getElementById('tab-content-json').style.display = tab === 'json' ? 'block' : 'none';
            document.getElementById('tab-content-results').style.display = tab === 'results' ? 'block' : 'none';
        }

        function renderMatchResults(results) {
            const container = document.getElementById('matchList');
            container.innerHTML = '';

            results.forEach((job, index) => {
                const score = parseFloat(job['Matching Score (%)']) || 0;
                const skills = typeof job['Kỹ năng phù hợp'] === 'string' ?
                    job['Kỹ năng phù hợp'].split(',').map(s => s.trim()) : [];

                const itemHTML = `
                <div class="match-item" style="background: rgba(255,255,255,0.05); padding: 16px; border-radius: 10px; margin-bottom: 16px; border-left: 4px solid #00ffaa; animation: fadeIn 0.5s ease forwards; animation-delay: ${index * 0.1}s;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:1.1rem; color:#00ffaa; margin-bottom:12px;">
                                ${job['Vị trí'] || 'Không có tiêu đề'}
                            </div>
                            <div style="color:#aaa; font-size:0.9rem;">
                                <strong>Lương:</strong> ${job['Mức lương'] || 'Thỏa thuận'} •
                                <strong>Kinh nghiệm:</strong> ${job['Kinh nghiệm'] || 'Không yêu cầu'} •
                                <strong>Địa điểm:</strong> ${job['Địa điểm'] || 'Không rõ'}
                            </div>
                            ${skills.length > 0 ? `
                                <div style="margin-top:10px; display:flex; flex-wrap:wrap; gap:8px;">
                                    ${skills.map(s => `<span style="background:rgba(0,180,255,0.2); color:#00b4ff; padding:4px 10px; border-radius:20px; font-size:0.8rem; border:1px solid rgba(0,180,255,0.4);">${s}</span>`).join('')}
                                </div>
                            ` : ''}
                        </div>
                        <div style="text-align:center;">
                            <div style="width:80px; height:80px; border-radius:50%; background:conic-gradient(#00ffaa calc(${score}%), rgba(255,255,255,0.1) 0); display:flex; align-items:center; justify-content:center; position:relative; margin:0 auto 12px;">
                                <div style="position:absolute; width:64px; height:64px; background:rgba(15,15,25,0.98); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:800; color:#00ffaa;">
                                    ${score.toFixed(1)}%
                                </div>
                            </div>
                            <div style="font-size:0.8rem; opacity:0.7;">Độ phù hợp</div>
                        </div>
                    </div>
                    ${job['url'] ? `<a href="${job['url']}" target="_blank" style="margin-top:16px; display:inline-block; background:#00b4ff; color:#000; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600;">Xem chi tiết ↗</a>` : ''}
                </div>`;
                container.insertAdjacentHTML('beforeend', itemHTML);
            });
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(() => successMessage.style.opacity = '0', 2000);
                setTimeout(() => successMessage.remove(), 3000);
            }
        });
    </script>
@endpush