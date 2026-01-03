<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSelector extends Model
{
    protected $table = 'site_selectors';
    protected $fillable = [
        'site',
        'page_type',
        'element_key',
        'selector_type',
        'selector_value',
        'description',
        'is_active',
        'version'
    ];
}
