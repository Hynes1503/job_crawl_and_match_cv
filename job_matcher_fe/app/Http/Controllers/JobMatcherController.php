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
use App\Helpers\CvTextExtractor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainingDataExport;
use Illuminate\Support\Facades\Response;

class JobMatcherController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:8080';

    /**
     * Trích xuất tóm tắt CV bằng Gemini API
     */
    private function extractCvSummary($cvText)
    {
        if (empty($cvText)) {
            return 'Nội dung CV không khả dụng';
        }
        $cvText = preg_replace('/[^\P{C}\n]+/u', '', $cvText);
        $cvText = preg_replace('/\s+/u', ' ', $cvText);
        $cvText = preg_replace('/([A-Za-zÀ-ỹ])\s+([a-zà-ỹ])/u', '$1$2', $cvText);
        $cvText = trim($cvText);
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            Log::warning('Gemini API key không được cấu hình.');
            return 'Lỗi: Thiếu API key Gemini';
        }

        $prompt = <<<PROMPT
Bạn là chuyên gia trích xuất thông tin CV. Hãy trích xuất các phần chính từ CV sau và trả về dưới dạng text ngắn gọn, có tiêu đề rõ ràng, mỗi phần cách nhau đúng 2 dòng trống.

Chỉ trả về nội dung trích xuất, không thêm bất kỳ giải thích nào.

Các phần cần có (nếu tồn tại trong CV):
- Tóm tắt / Giới thiệu / Mục tiêu nghề nghiệp
- Kinh nghiệm làm việc
- Học vấn / Trình độ học vấn
- Kỹ năng
- Chứng chỉ
- Dự án

CV:
{$cvText}
PROMPT;

        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
                [
                    "contents" => [
                        ["parts" => [["text" => $prompt]]]
                    ],
                    "generationConfig" => [
                        "temperature" => 0.3,
                        "maxOutputTokens" => 1500,
                    ],
                    "safetySettings" => [
                        [
                            "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                            "threshold" => "BLOCK_ONLY_HIGH"
                        ]
                    ]
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini API error: ' . $response->body());
                return 'Lỗi trích xuất CV (API không phản hồi)';
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            return $text ? trim($text) : 'Không trích xuất được nội dung từ CV';
        } catch (\Exception $e) {
            Log::error('Exception khi gọi Gemini API: ' . $e->getMessage());
            return 'Lỗi kết nối API trích xuất CV';
        }
    }

    /**
     * Hiển thị form matching CV (dùng dữ liệu mới nhất)
     */
    public function exportTrainingData($runId)
    {
        $crawlRun = CrawlRun::findOrFail($runId);

        if ($crawlRun->user_id !== Auth::id()) {
            abort(403);
        }

        // === Lấy thông tin CV ===
        $cvUsed = $crawlRun->cv_used ?? [];
        $cvId = $cvUsed['cv_id'] ?? null;

        $cvTextSummary = 'Nội dung CV không khả dụng';

        if ($cvId) {
            $cv = Cv::find($cvId);
            if ($cv && $cv->text_content) {
                // Gọi Gemini để trích xuất tóm tắt
                $cvTextSummary = $this->extractCvSummary($cv->text_content);
            }
        }

        // === Chuẩn bị dữ liệu job ===
        $details = $crawlRun->detail ?? [];
        $results = $crawlRun->result ?? [];

        $scoreMap = [];
        foreach ($results as $result) {
            if (isset($result['url'])) {
                $scoreMap[$result['url']] = $result['Matching Score (%)'] ?? '0';
            }
        }

        $rows = [];
        $rows[] = ['STT', 'CV ID', 'Nội dung CV (text)', 'Mức lương', 'Kinh nghiệm', 'Địa điểm', 'Matching Score (%)'];

        $index = 1;
        foreach ($details as $job) {
            $jobUrl = $job['url'] ?? '';
            $score = $scoreMap[$jobUrl] ?? '0';

            $salary = 'Thoả thuận';
            if (!empty($job['salary'])) {
                if (isset($job['salary']['max'])) {
                    $salary = 'Đến ' . $job['salary']['max'] . ' triệu';
                } elseif (is_array($job['salary'])) {
                    $salary = implode(', ', $job['salary']);
                }
            }

            $experience = 'Không yêu cầu';
            if (isset($job['experience']['min_years'])) {
                $min = $job['experience']['min_years'];
                $experience = is_numeric($min) ? $min . ' năm' : $min;
            }

            $rows[] = [
                $index++,
                $cvId ?? '',
                $cvTextSummary, // ← Đã được tóm tắt bằng AI
                $salary,
                $experience,
                $job['location'] ?? '',
                $score,
            ];
        }

        // === Tạo file CSV với UTF-8 BOM ===
        $filename = 'Training_Data_Run_' . $crawlRun->id . '_' . now()->format('Y_m_d_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-store, no-cache');
    }

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
            if (empty($usedCv->text_content)) {
                $cvContent = Storage::disk('public')->get($usedCv->file_path);
                $extension = pathinfo($usedCv->file_path, PATHINFO_EXTENSION);
                $textContent = CvTextExtractor::extract($cvContent, $extension);
                $usedCv->update(['text_content' => $textContent]);
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
