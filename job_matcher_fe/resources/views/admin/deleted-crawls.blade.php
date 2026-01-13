@extends('layouts.admin')

@section('title', 'Các Lần Crawl Đã Xóa - Job Matcher AI')

@push('styles')
    <style>
        .container {
            max-width: 1400px;
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

        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }

        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }

        .toast-notification {
            position: fixed;
            top: 24px;
            right: 24px;
            min-width: 350px;
            max-width: 450px;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 12px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            z-index: 9999;
            animation: slideIn 0.4s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toast-notification.hiding {
            animation: slideOut 0.4s ease-out forwards;
        }

        .toast-notification.success {
            border-left: 4px solid #00ffaa;
        }

        .toast-notification.error {
            border-left: 4px solid #ff5080;
        }

        .toast-notification.success .toast-icon {
            color: #00ffaa;
            font-size: 1.5rem;
        }

        .toast-notification.error .toast-icon {
            color: #ff5080;
            font-size: 1.5rem;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 4px;
            color: #fff;
        }

        .toast-message {
            font-size: 0.9rem;
            opacity: 0.85;
            color: #ddd;
        }

        .toast-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.1rem;
            cursor: pointer;
            padding: 4px 8px;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .toast-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .toast-notification::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #00b4ff, #00ffaa);
            animation: progressBar 4s linear forwards;
            border-radius: 0 0 12px 12px;
        }

        .toast-notification.error::after {
            background: linear-gradient(90deg, #ff5080, #ff8a00);
        }

        .filter-section {
            background: rgba(0, 0, 0, .35);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .custom-select {
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            backdrop-filter: blur(4px);
            border-radius: 8px;
        }

        .custom-select option {
            background: #1a1a2e !important;
            color: #ffffff !important;
        }

        .custom-select:focus {
            border-color: #00b4ff !important;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2) !important;
        }

        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            border-radius: 8px;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.08);
            border-color: #00b4ff;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.2);
        }

        .table-wrapper {
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 16px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th {
            background: rgba(255, 255, 255, .05);
            padding: 16px 12px;
            text-align: left;
            font-size: 0.92rem;
            font-weight: 600;
            opacity: .9;
            border: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }

        td {
            padding: 16px 12px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            font-size: 0.88rem;
            vertical-align: middle;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        tr:hover {
            background: rgba(0, 180, 255, 0.08);
        }

        .status {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-completed { background: rgba(0, 255, 150, .2); color: #00ffaa; border: 1px solid rgba(0, 255, 150, 0.4); }
        .status-running   { background: rgba(0, 180, 255, .2); color: #00b4ff; border: 1px solid rgba(0, 180, 255, 0.4); }
        .status-failed    { background: rgba(255, 100, 100, .2); color: #ff6b6b; border: 1px solid rgba(255, 100, 100, 0.4); }

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

        .btn-delete {
            background: rgba(255, 50, 80, 0.2);
            border: 1px solid rgba(255, 50, 80, 0.4);
            color: #ff5080;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: rgba(255, 50, 80, 0.3);
            border-color: #ff5080;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 50, 80, 0.3);
        }

        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #bbd0ff;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: rgba(0,180,255,0.2);
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
            padding: 80px 20px;
            opacity: .7;
            font-size: 1.2rem;
        }

        @keyframes textGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

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

    <div class="container">
        <div class="header">
            <h1 style="font-size: 2.4rem; margin-bottom: 12px;">
                <span style="background: linear-gradient(120deg, #00b4ff, #ffffff, #00b4ff); background-size: 200% 200%; animation: textGradient 6s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Các Lần Crawl Đã Xóa
                </span>
            </h1>
            <div class="stats">
                Tổng cộng <strong>{{ $deletedCrawls->total() }}</strong> lần crawl đã bị xóa
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('admin.deleted.crawls') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Từ khóa</label>
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                           placeholder="Nhập từ khóa...">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Địa điểm</label>
                    <input type="text" name="location" class="form-control" value="{{ request('location') }}"
                           placeholder="Ví dụ: Hà Nội">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Cấp bậc</label>
                    <input type="text" name="level" class="form-control" value="{{ request('level') }}"
                           placeholder="Ví dụ: Fresher">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Mức lương</label>
                    <input type="text" name="salary" class="form-control" value="{{ request('salary') }}"
                           placeholder="Ví dụ: 10-15 triệu">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Yêu cầu</label>
                    <input type="text" name="search_range" class="form-control" value="{{ request('search_range') }}"
                           placeholder="Ví dụ: 3 tháng">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Trạng thái</label>
                    <select name="status" class="form-select custom-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Thành công</option>
                        <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Đang chạy</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.9rem; opacity: 0.8;">Xóa bởi</label>
                    <input type="text" name="deleted_by" class="form-control" value="{{ request('deleted_by') }}"
                           placeholder="Tên người xóa...">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn w-100"
                            style="background: #00b4ff; border: none; padding: 10px; color: #000; font-weight: 600;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['keyword', 'location', 'level', 'salary', 'search_range', 'status', 'from_date', 'to_date', 'deleted_by']))
                <div class="mt-3 text-end">
                    <a href="{{ route('admin.deleted.crawls') }}" class="btn btn-outline-secondary"
                       style="border: 1px solid rgba(255,255,255,0.3); color: #bbd0ff; padding: 8px 16px;">
                        <i class="fas fa-times me-2"></i>Xóa bộ lọc
                    </a>
                </div>
            @endif
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
                            <th>Kết quả Match</th>
                            <th>Xóa bởi</th>
                            <th>Thời gian xóa</th>
                            <th style="text-align: center;">Thao Tác</th>
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
                                        <button class="action-btn match" onclick="openModal({{ $crawl->id }}, 'results')">
                                            Kết quả ({{ count($crawl->result) }})
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $crawl->deletedBy ? $crawl->deletedBy->name : 'N/A' }}</td>
                                <td>{{ $crawl->deleted_at->format('d/m/Y H:i:s') }}</td>
                                <td style="text-align: center;">
                                    <form action="{{ route('admin.deleted.crawls.destroy', $crawl->id) }}" method="POST" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn XÓA VĨNH VIỄN crawl này?\n\nHành động này KHÔNG THỂ KHÔI PHỤC!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Xóa vĩnh viễn">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $deletedCrawls->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-trash-alt fa-4x mb-4" style="opacity: 0.4;"></i>
                <p>Không tìm thấy lần crawl nào phù hợp với bộ lọc.</p>
            </div>
        @endif
    </div>

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

        function closeToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                setTimeout(() => {
                    closeToast();
                }, 4000);
            }
        });

        function openModal(runId, initialTab = 'json') {
            currentRunId = runId;
            const runData = crawlData[runId] || { detail: [], result: [] };
            const jobs = runData.detail || [];
            const results = runData.result || [];

            document.getElementById('modalTitle').textContent = `Crawl Đã Xóa #${runId} - ${jobs.length} công việc`;

            document.getElementById('jsonContent').textContent = JSON.stringify(jobs, null, 2);

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

        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush