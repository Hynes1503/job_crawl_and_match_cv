<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\DeletedCrawl;
use Illuminate\Support\Facades\Auth;
use App\Models\CrawlRun;

class AdminController extends Controller
{
    public function index()
    {

        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalNormalUsers = User::where('role', 'user')->count();
        $totalLogs = Log::count();

        $recentLogs = Log::with('user')->latest()->take(6)->get();

        $runs = CrawlRun::whereNotNull('detail')->get();

        $jobs = collect();

        foreach ($runs as $run) {
            foreach ($run->detail as $job) {
                $jobs->push([
                    'location'   => $job['location'] ?: 'Không rõ',
                    'created_at' => $run->created_at,
                    'hash'       => md5(($job['title'] ?? '') . ($job['location'] ?? '') . ($job['url'] ?? ''))
                ]);
            }
        }

        $crawlToday = $jobs->where('created_at', '>=', now()->startOfDay())->count();
        $crawlWeek  = $jobs->where('created_at', '>=', now()->startOfWeek())->count();
        $crawlMonth = $jobs->where('created_at', '>=', now()->startOfMonth())->count();
        $crawlYear  = $jobs->where('created_at', '>=', now()->startOfYear())->count();

        $duplicateJobs = $jobs->groupBy('hash')->filter(fn($i) => $i->count() > 1)->count();

        $byLocation = $jobs->groupBy('location')->map->count()->sortDesc();

        $topLocations = $byLocation->take(10);
        $othersCount = $byLocation->skip(10)->sum();

        if ($othersCount > 0) {
            $topLocations->put('Các tỉnh/thành khác', $othersCount);
        }

        $locationLabels = $topLocations->keys()->toArray();
        $locationData   = $topLocations->values()->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalNormalUsers',
            'totalLogs',
            'recentLogs',
            'crawlToday',
            'crawlWeek',
            'crawlMonth',
            'crawlYear',
            'duplicateJobs',
            'locationLabels',
            'locationData'
        ));
    }

    public function manageUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['user', 'admin'])) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderByRaw("FIELD(role, 'admin', 'user')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $users->appends($request->query());

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot change your own role!');
        }

        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update(['role' => $request->role]);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'update_user_role',
            'description' => "Updated role of user {$user->email} to {$request->role}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.users')->with('success', 'Role updated successfully');
    }


    public function deletedCrawls(Request $request)
    {
        $query = DeletedCrawl::with(['user', 'deletedBy'])->orderBy('deleted_at', 'desc');

        if ($request->filled('keyword')) {
            $query->where('parameters->keyword', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('location')) {
            $query->where('parameters->location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('level')) {
            $query->where('parameters->level', 'like', '%' . $request->level . '%');
        }

        if ($request->filled('salary')) {
            $query->where('parameters->salary', 'like', '%' . $request->salary . '%');
        }

        if ($request->filled('search_range')) {
            $query->where('parameters->search_range', 'like', '%' . $request->search_range . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('deleted_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('deleted_at', '<=', $request->to_date);
        }

        if ($request->filled('deleted_by')) {
            $query->whereHas('deletedBy', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->deleted_by . '%');
            });
        }

        $deletedCrawls = $query->paginate(20);

        return view('admin.deleted-crawls', compact('deletedCrawls'));
    }

    public function destroyDeletedCrawl($id)
    {
        try {
            $deletedCrawl = DeletedCrawl::find($id);

            if (!$deletedCrawl) {
                return redirect()->route('admin.deleted.crawls')
                    ->with('warning', 'Crawl #' . $id . ' đã được admin khác xóa trước đó!');
            }

            $deletedCrawl->delete();

            return redirect()->route('admin.deleted.crawls')
                ->with('success', 'Đã xóa vĩnh viễn crawl #' . $id . ' khỏi hệ thống!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.deleted.crawls')
                ->with('error', 'Không thể xóa crawl #' . $id . ' do lỗi hệ thống!');
        }
    }


    public function showDeletedCrawl(DeletedCrawl $deletedCrawl)
    {
        return view('admin.deleted-crawl-detail', compact('deletedCrawl'));
    }
}
