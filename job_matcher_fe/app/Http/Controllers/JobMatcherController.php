<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CrawlRun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Cv;
use Illuminate\Support\Facades\Storage;
use App\Models\Log as ActivityLog;
use App\Models\DeletedCrawl;

class JobMatcherController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:8080';

    /**
     * Hiển thị form matching CV (dùng dữ liệu mới nhất)
     */
    public function showMatchForm()
    {
        $jobsCount = 0;

        try {
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/jobs");
            if ($response->successful()) {
                $jobsCount = $response->json('jobs_count', 0);
            }
        } catch (\Exception $e) {
            Log::warning('Không thể lấy jobs count từ FastAPI: ' . $e->getMessage());
        }

        return view('match-cv', compact('jobsCount'));
    }

    /**
     * Xử lý matching CV với dữ liệu mới nhất
     */
    public function processMatch(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,docx,txt|max:10240',
            'extra_skills' => 'nullable|string|max:500',
            'desired_position' => 'nullable|string|max:255',
        ]);

        $cvFile = $request->file('cv_file');

        try {
            $response = Http::timeout(60)
                ->attach(
                    'cv_file',
                    file_get_contents($cvFile->path()),
                    $cvFile->getClientOriginalName()
                )
                ->post("{$this->apiBaseUrl}/match", [
                    'extra_skills' => $request->input('extra_skills', ''),
                    'desired_position' => $request->input('desired_position', ''),
                ]);

            if ($response->failed()) {
                $error = $response->json('detail') ?? $response->body();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Lỗi từ AI Engine: ' . $error);
            }

            $results = $response->json();

            if (empty($results)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Không tìm thấy công việc phù hợp nào.');
            }

            $topResults = array_slice($results, 0, 10);

            $formattedResults = array_map(function ($job) {
                return [
                    'Vị trí' => $job['title'] ?? 'Không rõ',
                    'Mức lương' => $job['salary'] ?? 'Thoả thuận',
                    'Kinh nghiệm' => is_numeric($job['experience'])
                        ? $job['experience'] . ' năm'
                        : ($job['experience'] ?? 'Không yêu cầu'),
                    'Địa điểm' => $job['location'] ?? 'Không xác định',
                    'Matching Score (%)' => number_format($job['score'], 1),
                    'Kỹ năng phù hợp' => is_array($job['matching_skills'] ?? null)
                        ? implode(', ', $job['matching_skills'])
                        : ($job['matching_skills'] ?? 'Không có'),
                    'url' => $job['url'] ?? '#',
                ];
            }, $topResults);

            return redirect()->back()
                ->withInput()
                ->with('results', $formattedResults);
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối FastAPI (match): ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không thể kết nối đến AI Engine. Vui lòng kiểm tra server FastAPI đang chạy.');
        }
        // Ghi log sau khi matching thành công
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'match_cv',
            'description' => 'Matched CV with latest jobs data',
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Matching CV với một lần crawl cụ thể (từ lịch sử)
     */
    public function matchWithRun(Request $request, $runId)
    {
        $request->validate([
            'existing_cv'     => 'nullable|exists:cvs,id',
            'cv_file'         => 'required_without:existing_cv|file|mimes:pdf,doc,docx,txt|max:10240', // thêm doc nếu cần
            'extra_skills'    => 'nullable|string|max:500',
            'desired_position' => 'nullable|string|max:255',
        ]);

        $crawlRun = CrawlRun::findOrFail($runId);

        if ($crawlRun->status !== 'completed' || !$crawlRun->detail || count($crawlRun->detail) == 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lần crawl này không có dữ liệu hợp lệ để matching.');
        }

        // Biến chung: Cv model cuối cùng được sử dụng để matching
        $usedCv = null;
        $cvContent = null;
        $cvName = null;

        try {
            // Xử lý CV: dùng CV cũ HOẶC upload CV mới → lưu thành Cv
            if ($request->filled('existing_cv')) {
                $usedCv = Cv::findOrFail($request->existing_cv);

                if ($usedCv->user_id !== Auth::id()) {
                    abort(403, 'Bạn không có quyền sử dụng CV này.');
                }

                $cvContent = Storage::disk('public')->get($usedCv->file_path);
                $cvName    = $usedCv->original_name;
            } else {
                // Upload CV mới
                $cvFile = $request->file('cv_file');

                // Tạo tên file unique để tránh trùng
                $extension = $cvFile->getClientOriginalExtension();
                $filename  = 'cv_' . Auth::id() . '_' . date('Y_m_d_His') . '_' . uniqid() . '.' . $extension;
                $filePath  = 'cvs/' . $filename;

                // Lưu file vào storage/public/cvs/
                Storage::disk('public')->put($filePath, file_get_contents($cvFile->path()));

                // Tạo bản ghi Cv mới trong DB
                $usedCv = Cv::create([
                    'user_id'   => Auth::id(),
                    'file_path' => $filePath,
                ]);

                $cvContent = file_get_contents($cvFile->path());
                $cvName    = $cvFile->getClientOriginalName();
            }

            // Bây giờ chắc chắn có $usedCv (Cv model), $cvContent và $cvName

            $response = Http::timeout(60)->attach(
                'cv_file',
                $cvContent,
                $cvName
            )->post("{$this->apiBaseUrl}/match-with-jobs", [
                'run_id'          => $crawlRun->id,
                'jobs_data'       => json_encode($crawlRun->detail),
                'extra_skills'    => $request->input('extra_skills', ''),
                'desired_position' => $request->input('desired_position', ''),
            ]);

            if ($response->failed()) {
                $error = $response->json('detail') ?? $response->body();

                // Nếu là CV mới upload, có thể xóa file và bản ghi nếu muốn (tùy chọn)
                // if (!$request->filled('existing_cv')) { $usedCv->delete(); Storage::disk('public')->delete($usedCv->file_path); }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Lỗi matching: ' . $error);
            }

            $results = $response->json();

            if (empty($results)) {
                $formattedResults = [];

                $crawlRun->update([
                    'result'   => [],
                    'cv_used'  => [
                        'cv_id'         => $usedCv->id,
                        'original_name' => $usedCv->original_name,
                        'url'           => $usedCv->url,
                        'uploaded_at'   => $usedCv->created_at->toDateTimeString(),
                    ],
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('info', 'Không tìm thấy công việc phù hợp nào trong lần crawl này.')
                    ->with('current_run_id', $crawlRun->id);
            }

            $topResults = array_slice($results, 0, 10);

            $formattedResults = array_map(function ($job) {
                return [
                    'Vị trí'            => $job['title'] ?? 'Không rõ',
                    'Mức lương'         => $job['salary'] ?? 'Thoả thuận',
                    'Kinh nghiệm'       => is_numeric($job['experience'])
                        ? $job['experience'] . ' năm'
                        : ($job['experience'] ?? 'Không yêu cầu'),
                    'Địa điểm'          => $job['location'] ?? 'Không xác định',
                    'Matching Score (%)' => number_format($job['score'], 1),
                    'Kỹ năng phù hợp'   => $job['matching_skills'] ?? 'Không có',
                    'url'               => $job['url'] ?? '#',
                ];
            }, $topResults);

            // Lưu kết quả + thông tin CV đã dùng
            $crawlRun->update([
                'result' => $formattedResults,
                'cv_used' => [
                    'cv_id'         => $usedCv->id,
                    'original_name' => $usedCv->original_name,
                    'url'           => $usedCv->url, // accessor getUrlAttribute()
                    'uploaded_at'   => $usedCv->created_at->toDateTimeString(),
                ],
            ]);

            // Ghi log activity
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'match_cv_with_run',
                'description' => "Matched CV ID {$usedCv->id} ({$usedCv->original_name}) with crawl run ID {$crawlRun->id}",
                'ip_address'  => request()->ip(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('current_run_id', $crawlRun->id)
                ->with('match_results', $formattedResults)
                ->with('match_run_info', "Kết quả matching với dữ liệu crawl ngày {$crawlRun->created_at->format('d/m/Y H:i')}")
                ->with('success', $request->filled('existing_cv')
                    ? 'Matching thành công!'
                    : 'CV mới đã được lưu tự động và matching thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi match với run {$runId}: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Không thể thực hiện matching với dữ liệu crawl này. Vui lòng thử lại sau.');
        }
    }

    /**
     * Gọi API crawl jobs + lưu lịch sử vào DB
     */
    public function crawlJobs(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'keyword'       => 'nullable|string|max:100',
            'location'      => 'nullable|string|max:100',
            'level'         => 'nullable|string|max:100',
            'salary'        => 'nullable|string|max:100',
            'search_range'  => 'nullable|integer|min:1|max:50',
        ]);

        set_time_limit(0);

        // Tạo bản ghi trước để theo dõi
        $crawlRun = CrawlRun::create([
            'user_id'       => $userId,
            'group_id'      => null,
            'source'        => 'topcv',
            'status'        => 'running',
            'parameters'    => $request->only(['keyword', 'location', 'level', 'salary', 'search_range']),
            'jobs_crawled'  => null,
            'detail'        => null,
            'error_message' => null,
        ]);

        try {
            $response = Http::timeout(0)
                ->asForm()
                ->post("{$this->apiBaseUrl}/crawl", [
                    'keyword'       => $request->input('keyword'),
                    'location'      => $request->input('location'),
                    'level'         => $request->input('level'),
                    'salary'        => $request->input('salary'),
                    'search_range'  => $request->input('search_range', 20),
                ]);

            if ($response->successful()) {
                // FastAPI chỉ trả về thông báo, không có jobs → gọi /jobs để lấy cleaned data
                $jobsResponse = Http::timeout(30)->get("{$this->apiBaseUrl}/jobs");

                $cleanedJobs = [];
                $jobsCount = 0;

                if ($jobsResponse->successful()) {
                    $data = $jobsResponse->json();
                    $cleanedJobs = $data['jobs'] ?? [];
                    $jobsCount = $data['jobs_count'] ?? count($cleanedJobs);
                }

                // Cập nhật thành công
                $crawlRun->update([
                    'status'       => 'completed',
                    'jobs_crawled' => $jobsCount,
                    'detail'       => $cleanedJobs,
                ]);

                // Ghi log sau khi crawl thành công
                ActivityLog::create([
                    'user_id' => $userId,
                    'action' => 'crawl_jobs',
                    'description' => "Crawled {$jobsCount} jobs with parameters: " . json_encode($request->only(['keyword', 'location', 'level', 'salary', 'search_range'])),
                    'ip_address' => request()->ip(),
                ]);

                return redirect()->back()->with('success', "Crawl thành công! Đã thu thập {$jobsCount} công việc.");
            }

            // Nếu crawl thất bại
            $errorDetail = $response->json('detail') ?? $response->body();
            $crawlRun->update([
                'status'        => 'failed',
                'error_message' => $errorDetail,
            ]);

            return redirect()->back()->with('error', 'Crawl thất bại: ' . $errorDetail);
        } catch (\Throwable $e) {
            Log::error("Crawl error (Run ID: {$crawlRun->id}): " . $e->getMessage());

            $crawlRun->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Không thể kết nối đến crawler. Vui lòng kiểm tra server FastAPI.');
        }
    }

    /**
     * Hiển thị form crawl
     */
    public function showCrawlForm()
    {
        $jobsCount = 0;

        try {
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/jobs");
            if ($response->successful()) {
                $jobsCount = $response->json('jobs_count', 0);
            }
        } catch (\Exception $e) {
            Log::warning('Không thể lấy jobs count: ' . $e->getMessage());
        }

        return view('crawl-jobs', compact('jobsCount'));
    }

    /**
     * Hiển thị lịch sử crawl
     */
    public function crawlHistory()
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để xem lịch sử crawl.');
        }
        $userCvs = auth()->user()->cvs()->latest()->get();
        $crawlRuns = auth()->user()
            ->crawlRuns()
            ->orderByDesc('created_at')
            ->paginate(10);

        $jobsCount = 0;
        try {
            $response = Http::timeout(8)->get("{$this->apiBaseUrl}/jobs");
            if ($response->successful()) {
                $jobsCount = $response->json('jobs_count', 0);
            }
        } catch (\Exception $e) {
            Log::warning('Jobs count API unreachable: ' . $e->getMessage());
        }
        $crawlData = $crawlRuns->mapWithKeys(function ($run) {
            return [
                $run->id => [
                    'detail' => $run->detail ?? [],
                    'result' => $run->result ?? [],
                ],
            ];
        })->toArray();

        return view('crawl-history', compact(
            'crawlRuns',
            'jobsCount',
            'crawlData',
            'userCvs'
        ));
    }

    public function destroy(CrawlRun $crawlRun)
    {
        // Bảo vệ: chỉ cho phép người dùng xóa crawl run của chính mình
        if ($crawlRun->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa lần crawl này.');
        }

        // Sao chép dữ liệu vào bảng deleted_crawls trước khi xóa
        DeletedCrawl::create([
            'user_id' => $crawlRun->user_id,
            'group_id' => $crawlRun->group_id,
            'source' => $crawlRun->source,
            'status' => $crawlRun->status,
            'parameters' => $crawlRun->parameters,
            'jobs_crawled' => $crawlRun->jobs_crawled,
            'error_message' => $crawlRun->error_message,
            'detail' => $crawlRun->detail,
            'result' => $crawlRun->result,
            'deleted_by' => Auth::id(),
            'deleted_at' => now(),
            'created_at' => $crawlRun->created_at,
            'updated_at' => $crawlRun->updated_at,
        ]);

        // Xóa record từ crawl_runs
        $crawlRun->delete();

        // Ghi log sau khi xóa crawl run
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_crawl_run',
            'description' => "đã xóa crawl run ID {$crawlRun->id}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->back()
            ->with('success', 'Đã xóa lần crawl thành công.');
    }
}
