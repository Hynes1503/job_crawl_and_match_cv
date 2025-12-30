<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Log; // Thêm model Log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    public function index()
    {
        $cvs = Auth::user()->cvs()->latest()->get();

        return view('cv.index', compact('cvs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240', // max 10MB
        ]);

        $file = $request->file('cv');
        $originalName = $file->getClientOriginalName();

        // Tạo tên file duy nhất
        $filename = Auth::id() . '_' . time() . '_' . $originalName;
        $path = $file->storeAs('cvs', $filename, 'public');

        // Lưu CV vào DB
        $cv = Auth::user()->cvs()->create([
            'file_path' => $path,
            'original_name' => $originalName, // lưu thêm tên gốc để hiển thị đẹp hơn
        ]);

        // === GHI LOG ===
        Log::create([
            'user_id'     => Auth::id(),
            'action'      => 'upload_cv',
            'description' => "Đã upload CV: {$originalName}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'CV đã được upload thành công!');
    }

    public function destroy(Cv $cv)
    {
        // Kiểm tra quyền sở hữu
        if ($cv->user_id !== Auth::id()) {
            abort(403);
        }

        $cvName = $cv->original_name ?? basename($cv->file_path);

        // Xóa file vật lý
        if (Storage::disk('public')->exists($cv->file_path)) {
            Storage::disk('public')->delete($cv->file_path);
        }

        // Xóa record CV
        $cv->delete();

        // === GHI LOG ===
        Log::create([
            'user_id'     => Auth::id(),
            'action'      => 'delete_cv',
            'description' => "Đã xóa CV: {$cvName}",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->back()->with('success', 'CV đã được xóa thành công!');
    }
}