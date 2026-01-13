<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_cvs' => $user->cvs()->count(),
            'total_crawls' => $user->crawlRuns()->count(),
            'pending_crawls' => $user->crawlRuns()->where('status', 'pending')->count(),
            'completed_crawls' => $user->crawlRuns()->where('status', 'completed')->count(),
            'failed_crawls' => $user->crawlRuns()->where('status', 'failed')->count(),
            'total_jobs_found' => $user->crawlRuns()->sum('jobs_crawled'),
        ];

        $recentCvs = $user->cvs()
            ->latest()
            ->take(5)
            ->get();

        $recentCrawls = $user->crawlRuns()
            ->latest()
            ->take(5)
            ->get();

        $crawlsBySource = $user->crawlRuns()
            ->selectRaw('source, count(*) as total, sum(jobs_crawled) as jobs')
            ->groupBy('source')
            ->get();

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
