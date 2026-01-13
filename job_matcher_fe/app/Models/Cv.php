<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $table = 'cvs';

    protected $fillable = [
        'user_id',
        'file_path',
        'text_content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOriginalNameAttribute()
    {
        return basename($this->file_path);
    }
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

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