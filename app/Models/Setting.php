<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key','value','type'];

    public $timestamps = true;

    /**
     * Helper: get setting by key with optional default
     */
    public static function get($key, $default = null)
    {
        $s = static::where('key', $key)->first();
        return $s ? $s->value : $default;
    }

    /**
     * Helper: set a setting
     */
    public static function set($key, $value, $type = 'string')
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }
}
