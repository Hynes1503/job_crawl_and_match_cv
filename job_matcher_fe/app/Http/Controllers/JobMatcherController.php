<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CrawlRun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
    }

    /**
     * Matching CV với một lần crawl cụ thể (từ lịch sử)
     */
    public function matchWithRun(Request $request, $runId)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,docx,txt|max:10240',
            'extra_skills' => 'nullable|string|max:500',
            'desired_position' => 'nullable|string|max:255',
        ]);

        $crawlRun = CrawlRun::findOrFail($runId);

        if ($crawlRun->status !== 'completed' || !$crawlRun->detail || count($crawlRun->detail) == 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lần crawl này không có dữ liệu hợp lệ để matching.');
        }

        $cvFile = $request->file('cv_file');

        try {
            $response = Http::timeout(60)
                ->attach(
                    'cv_file',
                    file_get_contents($cvFile->path()),
                    $cvFile->getClientOriginalName()
                )
                ->post("{$this->apiBaseUrl}/match-with-jobs", [
                    'run_id' => $crawlRun->id,
                    'jobs_data' => json_encode($crawlRun->detail),
                    'extra_skills' => $request->input('extra_skills', ''),
                    'desired_position' => $request->input('desired_position', ''),
                ]);

            if ($response->failed()) {
                $error = $response->json('detail') ?? $response->body();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Lỗi matching: ' . $error);
            }

            $results = $response->json();

            if (empty($results)) {
                // Lưu kết quả rỗng để biết đã matching nhưng không có job phù hợp
                $crawlRun->update([
                    'result' => [],
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('info', 'Không tìm thấy công việc phù hợp nào trong lần crawl này.');
            }

            $topResults = array_slice($results, 0, 10);

            $formattedResults = array_map(function ($job) use ($crawlRun) {
                return [
                    'Vị trí' => $job['title'] ?? 'Không rõ',
                    'Mức lương' => $job['salary'] ?? 'Thoả thuận',
                    'Kinh nghiệm' => is_numeric($job['experience'])
                        ? $job['experience'] . ' năm'
                        : ($job['experience'] ?? 'Không yêu cầu'),
                    'Địa điểm' => $job['location'] ?? 'Không xác định',
                    'Matching Score (%)' => number_format($job['score'], 1),
                    'Kỹ năng phù hợp' => $job['matching_skills'] ?? 'Không có',
                    'url' => $job['url'] ?? '#',
                ];
            }, $topResults);

            // Lưu kết quả vào DB
            $crawlRun->update([
                'result' => $formattedResults,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('current_run_id', $crawlRun->id)
                ->with('match_results', $formattedResults)
                ->with('match_run_info', "Kết quả matching với dữ liệu crawl ngày {$crawlRun->created_at->format('d/m/Y H:i')}");
        } catch (\Exception $e) {
            Log::error("Lỗi match với run {$runId}: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không thể thực hiện matching với dữ liệu crawl này.');
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
            'crawlData'
        ));
    }

    public function destroy(CrawlRun $crawlRun)
    {
        // Bảo vệ: chỉ cho phép người dùng xóa crawl run của chính mình
        if ($crawlRun->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa lần crawl này.');
        }

        // Xóa dữ liệu (detail và result là array lớn, nên xóa record luôn là sạch nhất)
        $crawlRun->delete();

        return redirect()->back()
            ->with('success', 'Đã xóa lần crawl thành công.');
    }
}
