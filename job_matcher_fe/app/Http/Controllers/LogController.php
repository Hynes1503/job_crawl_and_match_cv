<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = Log::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }

    // Các phương thức khác có thể để trống hoặc xóa nếu không cần
}
