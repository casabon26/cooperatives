<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','video_id','current_time','total_duration','progress_percent'];

    public function user() { return $this->belongsTo(User::class); }
}
