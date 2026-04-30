<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'path',
        'alt_text',
        'published',
        'section',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->path)) return null;
        if (preg_match('#^https?://#', $this->path)) return $this->path;
        $p = ltrim($this->path, '/');
        $storagePath = storage_path('app/public/' . $p);
        if (file_exists($storagePath)) return asset('storage/' . $p);
        if (file_exists(public_path($p))) return asset($p);
        $fallback = 'gallery_images/' . basename($p);
        if (file_exists(public_path($fallback))) return asset($fallback);
        return asset($p);
    }
}
