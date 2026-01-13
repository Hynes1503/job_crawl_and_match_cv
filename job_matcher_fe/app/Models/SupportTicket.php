<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'priority',
        'status',
        'last_reply_by'
    ];

    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
    public function scopeActive($q)
    {
        return $q->whereIn('status', ['open', 'processing']);
    }

    public static function countForAdminNavbar()
    {
        return static::active()->count();
    }
}
