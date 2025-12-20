<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'source',
        'status',
        'parameters',
        'jobs_crawled',
        'error_message',
        'detail',
        'result',
    ];

    protected $casts = [
        'parameters' => 'array',
        'detail'     => 'array',
        'result'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}