<?php

namespace App\Policies;

use App\Models\Cooperative;
use App\Models\User;

class CooperativePolicy
{
    public function manage(User $user, Cooperative $cooperative)
    {
        if ($user->role === 'gov_admin') return true;
        // cooperative_admin users should be members of the cooperative (via pivot table)
        if ($user->role === 'cooperative_admin') {
            return $user->cooperatives()->where('cooperative_id',$cooperative->id)->exists();
        }
        return false;
    }
}
