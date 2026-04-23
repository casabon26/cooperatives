<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SimpleAdminAuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLogin()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Authenticate against the database (hashed password only)
        $user = User::where('email', $data['email'])->first();
        if ($user && Hash::check($data['password'], $user->password)) {
            // Ensure this is an admin account
            if ($user->role !== 'gov_admin') {
                return back()->withErrors(['email' => 'Not an admin account.'])->withInput();
            }

            // Mark the simple admin session and log in
            session(['admin_authenticated' => true, 'admin_email' => $user->email]);
            Auth::login($user);

            // Ensure AdminProfile exists
            AdminProfile::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'name' => $user->name ?? 'System Administrator',
                'email' => $user->email,
            ]);

            $request->session()->regenerate();
            return redirect('/admin/panel')->with('success', 'Logged in successfully.');
        }

        return back()->withErrors(['email' => 'Invalid email or password'])->withInput();
    }

    /**
     * Show admin panel
     */
    public function panel()
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        return view('admin.panel');
    }

    /**
     * Show admin profile page
     */
    public function showProfile()
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        $user = Auth::user();
        $profile = AdminProfile::where('user_id', $user->id)->first();

        return view('admin.profile.show', compact('user', 'profile'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        $user = Auth::user();
        $profile = AdminProfile::where('user_id', $user->id)->first();

        return view('admin.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update profile information (name, email, phone, bio)
     */
    public function updateProfile(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $emailChanged = $user->email !== $validated['email'];
        
        // Update user in database
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update or create admin profile
        $profile = AdminProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $validated['name']]
        );

        $profile->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        // If email changed, log out and require re-login with new credentials
        if ($emailChanged) {
            session()->flush();
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/admin/login')->with('success', 'Email updated successfully. Please log in with your new email address.');
        }

        return redirect('/admin/profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        return view('admin.profile.change-password');
    }

    /**
     * Update password securely
     */
    public function updatePassword(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:12|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).',
            'new_password.min' => 'Password must be at least 12 characters long.',
        ]);

        $user = Auth::user();

        // Verify current password against hashed password in database
        $passwordIsCorrect = Hash::check($validated['current_password'], $user->password);

        if (!$passwordIsCorrect) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        // Hash new password using bcrypt with 12 rounds (Laravel default)
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Update admin profile
        $profile = AdminProfile::where('user_id', $user->id)->first();
        if ($profile) {
            $profile->update([
                'password_changed_at' => now(),
            ]);
        }

        // Log out user and require login with new password
        session()->flush();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Password changed successfully. Please log in with your new password.');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        session()->flush();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Logged out successfully.');
    }
}
