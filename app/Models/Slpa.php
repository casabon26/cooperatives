<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slpa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'gallery',
        'members_count',
        'address',
        'products',
        'business',
    ];

    protected $casts = [
        'members_count' => 'integer',
        'gallery' => 'array',
        'products' => 'array',
    ];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            if (preg_match('#^https?://#', $this->image)) return $this->image;
            if (file_exists(public_path('storage/' . ltrim($this->image, '/')))) return asset('storage/' . ltrim($this->image, '/'));
            if (file_exists(public_path(ltrim($this->image, '/')))) return asset(ltrim($this->image, '/'));
            // also check public fallback copy location (slpa_images)
            $fallback = str_replace('slpas/', 'slpa_images/', ltrim($this->image, '/'));
            if (file_exists(public_path($fallback))) return asset($fallback);
        }
        // fallback to bundled default image (if present)
        if (file_exists(public_path('assets/images/default-slpa.svg'))) {
            return asset('assets/images/default-slpa.svg');
        }
        return null;
    }
}
