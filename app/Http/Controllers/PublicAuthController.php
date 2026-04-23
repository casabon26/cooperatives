<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PublicAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        // Prevent admin accounts from logging in via the public/user login form
        if ($user && ($user->role ?? '') === 'gov_admin') {
            return back()->with('error', 'This account is an admin account. Please use the Admin Login page.')->withInput();
        }

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Logged in successfully.');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'cp_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'sex' => 'required|in:Male,Female',
            'age' => 'nullable|integer|min:0|max:150',
            'birthday' => 'nullable|date',
        ]);

        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'cp_number' => $data['cp_number'] ?? null,
                'address' => $data['address'] ?? null,
                'sex' => $data['sex'] ?? null,
                'age' => $data['age'] ?? null,
                'birthday' => $data['birthday'] ?? null,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Registration successful. You are now logged in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
