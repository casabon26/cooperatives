<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CooperativeProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['cooperative_id','objectives','services','contact_info'];

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }
}
