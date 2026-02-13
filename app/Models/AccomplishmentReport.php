<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccomplishmentReport extends Model
{
    protected $table = 'accomplishment_reports';

    protected $fillable = [
        'code', 'title', 'content', 'file_path', 'published_at'
    ];

    protected $dates = ['published_at', 'created_at', 'updated_at'];
    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
