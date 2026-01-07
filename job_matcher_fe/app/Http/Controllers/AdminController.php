<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\DeletedCrawl;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function manageUsers(Request $request)
    {
        $query = User::query();

        // Lọc theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if ($request->filled('role') && in_array($request->input('role'), ['user', 'admin'])) {
            $query->where('role', $request->input('role'));
        }

        // ƯU TIÊN: Admin lên trên đầu, sau đó mới đến User
        // Sau đó sắp xếp theo ngày tạo giảm dần trong từng nhóm
        $users = $query->orderByRaw("FIELD(role, 'admin', 'user')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $users->appends($request->query());

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update(['role' => $request->role]);

        // Ghi log cập nhật role
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

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $query->where('parameters->keyword', 'like', '%' . $request->keyword . '%');
        }

        // Lọc theo địa điểm
        if ($request->filled('location')) {
            $query->where('parameters->location', 'like', '%' . $request->location . '%');
        }

        // Lọc theo cấp bậc
        if ($request->filled('level')) {
            $query->where('parameters->level', 'like', '%' . $request->level . '%');
        }

        // Lọc theo mức lương
        if ($request->filled('salary')) {
            $query->where('parameters->salary', 'like', '%' . $request->salary . '%');
        }

        // Lọc theo yêu cầu (search_range)
        if ($request->filled('search_range')) {
            $query->where('parameters->search_range', 'like', '%' . $request->search_range . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày xóa
        if ($request->filled('from_date')) {
            $query->whereDate('deleted_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('deleted_at', '<=', $request->to_date);
        }

        // Lọc theo người xóa
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
