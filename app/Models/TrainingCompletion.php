<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCompletion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','video_id','completed_at','certificate_token'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function video() { return $this->belongsTo(Video::class); }
}
