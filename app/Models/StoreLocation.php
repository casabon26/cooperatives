<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
    protected $fillable = [
        'name', 'address', 'plus_code', 'lat', 'lng', 'description', 'icon_url', 'category', 'tags', 'map_url', 'item_map_url'
    ];
}
