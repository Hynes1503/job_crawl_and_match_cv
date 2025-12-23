<?php

namespace App\Http\Controllers;

use App\Models\Cv;
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

        // Tạo đường dẫn lưu: cvs/{user_id}_{timestamp}_{original_name}
        $filename = Auth::id() . '_' . time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('cvs', $filename, 'public'); 
        // → lưu vào storage/app/public/cvs/...

        Auth::user()->cvs()->create([
            'file_path' => $path, // chỉ lưu đường dẫn tương đối: cvs/filename.pdf
        ]);

        return redirect()->back()->with('success', 'CV đã được upload thành công!');
    }

    public function destroy(Cv $cv)
    {
        if ($cv->user_id !== Auth::id()) {
            abort(403);
        }

        // Xóa file vật lý
        if (Storage::disk('public')->exists($cv->file_path)) {
            Storage::disk('public')->delete($cv->file_path);
        }

        // Xóa record
        $cv->delete();

        return redirect()->back()->with('success', 'CV đã được xóa thành công!');
    }
}