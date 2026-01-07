<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');

        // Lọc theo người dùng (tên hoặc email)
        if ($request->filled('user')) {
            $search = $request->input('user');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo hành động
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Lọc theo IP
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->input('ip') . '%');
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        // Sắp xếp mới nhất trước
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Giữ query string cho phân trang và form
        $logs->appends($request->query());

        return view('admin.logs.index', compact('logs'));
    }

    public function destroy($id)
    {
        try {
            $log = Log::find($id);

            if (!$log) {
                return redirect()->route('admin.logs.index')
                    ->with('warning', 'Bản ghi log #' . $id . ' đã được admin khác xóa trước đó!');
            }

            $log->delete();

            return redirect()->route('admin.logs.index')
                ->with('success', 'Đã xóa bản ghi log thành công.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.logs.index')
                ->with('error', 'Không thể xóa log #' . $id . ' do lỗi hệ thống.');
        }
    }
}
