<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
    protected $table = 'cabstop_stores';
    
    protected $fillable = [
        'name', 'owner_name', 'status', 'address', 'lat', 'lng', 'description', 'icon_url', 'category', 'tags', 'map_url', 'place', 'store_type'
    ];
}
