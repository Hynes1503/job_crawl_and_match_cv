<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $table = 'cvs'; // Đảm bảo dùng đúng tên bảng

    protected $fillable = [
        'user_id',
        'file_path',
        'text_content',
        // 'embedding' không cần fillable vì thường được tính sau
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tên file gốc từ file_path (giả sử file_path lưu đầy đủ như: cvs/user_1_cv_2025.pdf)
    public function getOriginalNameAttribute()
    {
        return basename($this->file_path);
    }

    // URL public để xem/tải CV
    public function getUrlAttribute()
    {
        // Giả sử file được lưu trong storage/app/public/[file_path]
        // Ví dụ: file_path = "cvs/cv_nguyenvana.pdf" → public/storage/cvs/...
        return asset('storage/' . $this->file_path);
    }

    // Mime type ước lượng từ phần mở rộng (nếu cần hiển thị)
    public function getMimeTypeAttribute()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        return match(strtolower($extension)) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }
}