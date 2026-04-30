<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectListItem extends Model
{
    protected $table = 'select_list_items';

    protected $fillable = [
        'group', 'label', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
