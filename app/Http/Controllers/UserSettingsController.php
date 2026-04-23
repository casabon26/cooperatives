<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('user.settings', compact('user'));
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

        // Update fields
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

        return redirect()->route('settings.show')->with('success', 'Settings updated successfully.');
    }
}
