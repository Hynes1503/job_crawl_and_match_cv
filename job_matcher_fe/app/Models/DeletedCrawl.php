<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedCrawl extends Model
{
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
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'detail' => 'array',
        'result' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
