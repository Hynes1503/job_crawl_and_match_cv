<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\DeletedCrawl;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function manageUsers()
    {
        $users = User::all();
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

    public function deletedCrawls()
    {
        $deletedCrawls = DeletedCrawl::with(['user', 'deletedBy'])->orderBy('deleted_at', 'desc')->paginate(20);
        return view('admin.deleted-crawls', compact('deletedCrawls'));
    }

    public function showDeletedCrawl(DeletedCrawl $deletedCrawl)
    {
        return view('admin.deleted-crawl-detail', compact('deletedCrawl'));
    }
}
