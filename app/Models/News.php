<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title','content','published_at','created_by','image','card_slot','image_data','image_mime','image_filename'];

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class,'created_by');
    }
}
