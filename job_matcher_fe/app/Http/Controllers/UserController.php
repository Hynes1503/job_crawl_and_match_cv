<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Thống kê tổng quan
        $stats = [
            'total_cvs' => $user->cvs()->count(),
            'total_crawls' => $user->crawlRuns()->count(),
            'pending_crawls' => $user->crawlRuns()->where('status', 'pending')->count(),
            'completed_crawls' => $user->crawlRuns()->where('status', 'completed')->count(),
            'failed_crawls' => $user->crawlRuns()->where('status', 'failed')->count(),
            'total_jobs_found' => $user->crawlRuns()->sum('jobs_crawled'),
        ];

        // Danh sách CV gần đây (5 CV mới nhất)
        $recentCvs = $user->cvs()
            ->latest()
            ->take(5)
            ->get();

        // Lịch sử crawl gần đây (10 lần gần nhất)
        $recentCrawls = $user->crawlRuns()
            ->latest()
            ->take(5)
            ->get();

        // Thống kê theo nguồn
        $crawlsBySource = $user->crawlRuns()
            ->selectRaw('source, count(*) as total, sum(jobs_crawled) as jobs')
            ->groupBy('source')
            ->get();

        // Thống kê theo trạng thái
        $crawlsByStatus = $user->crawlRuns()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentCvs',
            'recentCrawls',
            'crawlsBySource',
            'crawlsByStatus'
        ));
    }
}
