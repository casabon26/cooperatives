<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'bio',
        'password_changed_at',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
    ];

    /**
     * Relationship: has one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
