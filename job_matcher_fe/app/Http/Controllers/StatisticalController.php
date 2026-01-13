<?php

namespace App\Http\Controllers;

use App\Models\CrawlRun;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StatisticalController extends Controller
{
    public function index()
    {
        $runs = CrawlRun::whereNotNull('detail')->get();

        $jobs = collect();

        foreach ($runs as $run) {
            foreach ($run->detail as $job) {
                $jobs->push([
                    'title'      => $job['title'] ?? '',
                    'location'   => $job['location'] ?: 'Không rõ',
                    'salary_max' => $job['salary']['max'] ?? null,
                    'url'        => $job['url'] ?? '',
                    'created_at' => Carbon::parse($run->created_at),
                    'hash'       => md5(($job['title'] ?? '') . ($job['location'] ?? '') . ($job['url'] ?? ''))
                ]);
            }
        }

        $totalJobs = $jobs->count();
        $todayJobs = $jobs->where('created_at', '>=', now()->startOfDay())->count();
        $weekJobs  = $jobs->where('created_at', '>=', now()->startOfWeek())->count();
        $monthJobs = $jobs->where('created_at', '>=', now()->startOfMonth())->count();

        $byLocation = $jobs->groupBy('location')->map->count()->sortDesc();

        $salaryStats = [
            'Thoả thuận'     => $jobs->whereNull('salary_max')->count(),
            'Dưới 10 triệu'  => $jobs->whereBetween('salary_max', [0, 10])->count(),
            '10 - 15 triệu'  => $jobs->whereBetween('salary_max', [10, 15])->count(),
            '15 - 20 triệu'  => $jobs->whereBetween('salary_max', [15, 20])->count(),
            '20 - 25 triệu'  => $jobs->whereBetween('salary_max', [20, 25])->count(),
            '25 - 30 triệu'  => $jobs->whereBetween('salary_max', [25, 30])->count(),
            '30 - 50 triệu'  => $jobs->whereBetween('salary_max', [30, 50])->count(),
            'Trên 50 triệu'  => $jobs->where('salary_max', '>', 50)->count(),
        ];

        $duplicateJobs = $jobs->groupBy('hash')->filter(fn($i) => $i->count() > 1)->count();

        $topKeywords = $this->extractKeywords($jobs);

        $trendStacks = ['php','java','python','react','node','devops','ai','data'];
        $trendResult = [];

        foreach ($trendStacks as $stack) {
            $daily = $this->buildTrend($jobs, $stack);
            $trendResult[$stack] = [
                'daily' => $daily,
                'ma7'   => $this->movingAverage($daily)
            ];
        }

        return view('admin.statistics.index', [
            'totalJobs' => $totalJobs,
            'todayJobs' => $todayJobs,
            'weekJobs' => $weekJobs,
            'monthJobs' => $monthJobs,
            'byLocationLabels' => $byLocation->keys(),
            'byLocationData' => $byLocation->values(),
            'salaryLabels' => array_keys($salaryStats),
            'salaryData' => array_values($salaryStats),
            'duplicateJobs' => $duplicateJobs,

            'topKeywordLabels' => $topKeywords->keys(),
            'topKeywordData'   => $topKeywords->values(),
            'trendStacks' => $trendResult
        ]);
    }

    private function extractKeywords($jobs)
    {
        $stopWords = [
            'tuyển','cần','nhân','viên','developer','dev','engineer','software',
            'lập','trình','programmer','senior','junior','middle','level','it'
        ];

        $keywords = [];

        foreach ($jobs as $job) {
            $title = mb_strtolower($job['title']);
            $title = preg_replace('/[^a-z0-9\s]/u', ' ', $title);
            $words = array_filter(explode(' ', $title));

            foreach ($words as $word) {
                if (mb_strlen($word) >= 3 && !in_array($word, $stopWords)) {
                    $keywords[] = $word;
                }
            }
        }

        return collect($keywords)->countBy()->sortDesc()->take(20);
    }

    private function buildTrend($jobs, $keyword)
    {
        return $jobs->filter(fn($j) => str_contains(mb_strtolower($j['title']), $keyword))
            ->groupBy(fn($j) => $j['created_at']->format('Y-m-d'))
            ->map->count()
            ->sortKeys();
    }

    private function movingAverage($data, $window = 7)
    {
        $values = $data->values();
        $dates  = $data->keys();

        $ma = [];

        for ($i = 0; $i < count($values); $i++) {
            $slice = $values->slice(max(0, $i - $window + 1), $window);
            $ma[$dates[$i]] = round($slice->avg(), 2);
        }

        return collect($ma);
    }
}
