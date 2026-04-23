<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile with credentials.
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }
}
