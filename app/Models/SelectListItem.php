<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectListItem extends Model
{
    protected $table = 'select_list_items';

    protected $fillable = [
        'group', 'key', 'label', 'sort_order', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
