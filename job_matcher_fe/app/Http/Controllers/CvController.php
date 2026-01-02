<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Log; // Model Log của bạn
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\CvTextExtractor; // <-- Đảm bảo helper này tồn tại

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
        $extension = strtolower($file->getClientOriginalExtension());

        // Tạo tên file duy nhất
        $filename = Auth::id() . '_' . time() . '_' . uniqid() . '.' . $extension;
        $path = $file->storeAs('cvs', $filename, 'public');

        // === EXTRACT TEXT từ file CV ===
        $fileContent = file_get_contents($file->getRealPath());
        $textContent = CvTextExtractor::extract($fileContent, $extension);

        // Nếu extract thất bại hoặc rỗng, vẫn lưu CV nhưng để text_content = null
        // (Bạn có thể log warning nếu muốn)

        // Lưu CV vào DB kèm text_content
        $cv = Auth::user()->cvs()->create([
            'file_path'     => $path,
            'original_name' => $originalName,
            'text_content'  => $textContent ?: null, // Lưu text đã extract
        ]);

        // === GHI LOG ===
        Log::create([
            'user_id'     => Auth::id(),
            'action'      => 'upload_cv',
            'description' => "Đã upload CV: {$originalName}" . ($textContent ? ' (đã extract text)' : ' (không extract được text)'),
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'CV đã được upload và xử lý thành công!');
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

        // Xóa record CV (kèm text_content)
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