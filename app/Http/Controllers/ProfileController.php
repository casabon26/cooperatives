<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingCompletion;
use App\Models\Video;

use Illuminate\Support\Facades\Hash;

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

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'cp_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'sex' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->first_name = $data['first_name'] ?? $user->first_name;
        $user->last_name = $data['last_name'] ?? $user->last_name;
        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'];
        $user->cp_number = $data['cp_number'] ?? $user->cp_number;
        $user->address = $data['address'] ?? $user->address;
        $user->sex = $data['sex'] ?? $user->sex;
        $user->birthday = $data['birthday'] ?? $user->birthday;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function certificates()
    {
        $user = Auth::user();
        $certs = TrainingCompletion::with('video')->where('user_id', $user->id)->orderByDesc('completed_at')->get();
        return view('profile.certificates', compact('user','certs'));
    }
}
