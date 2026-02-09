<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooperative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','sector','region','description','status','link'];

    public function profile()
    {
        return $this->hasOne(CooperativeProfile::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class)->withTimestamps()->withPivot('role');
    }
}
