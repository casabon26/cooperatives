<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooperative extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'members_count' => 'integer',
    ];

    protected $fillable = [
        'name','sector','region','description','link','status','image',
        'objectives','services','contact_info','mission','vision','achievements','years','members_count','address','gallery',
        'contact_phone','contact_email','facebook','twitter','instagram','linkedin','map_embed','operating_hours'
    ];
    public function profile()
    {
        return $this->hasOne(CooperativeProfile::class);
    }

    /**
     * Safe accessor for the `profile` dynamic property.
     * If the `cooperative_profiles` table is missing or a query error occurs,
     * return null instead of throwing an exception so listing pages remain available.
     */
    public function getProfileAttribute()
    {
        try {
            if ($this->relationLoaded('profile')) {
                return $this->getRelation('profile');
            }
            $rel = $this->profile()->first();
            $this->setRelation('profile', $rel);
            return $rel;
        } catch (\Throwable $e) {
            return null;
        }
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
