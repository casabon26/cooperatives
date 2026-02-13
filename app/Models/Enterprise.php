<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    protected $fillable = ['name','category','summary','description','image'];

    public function getImageUrlAttribute()
    {
        if(!$this->image) return null;
        // image stored in storage/app/public/enterprises or as a direct path
        if(str_starts_with($this->image, 'http')) return $this->image;
        if(file_exists(public_path('storage/'.$this->image))) return asset('storage/'.$this->image);
        if(file_exists(public_path($this->image))) return asset($this->image);
        return null;
    }
}
