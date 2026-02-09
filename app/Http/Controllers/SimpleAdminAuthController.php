<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SimpleAdminAuthController extends Controller
{
    // Hardcoded admin credentials
    private $adminEmail = 'admin@portal.local';
    private $adminPassword = 'AdminPass123!';

    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($data['email'] === $this->adminEmail && $data['password'] === $this->adminPassword) {
            // mark the simple admin session
            session(['admin_authenticated' => true, 'admin_email' => $this->adminEmail]);

            // Also ensure the simple admin is logged into Laravel's auth so admin-prefixed
            // routes protected by `auth` middleware work as expected.
            $laravelAdmin = User::where('role','gov_admin')->first();
            if (! $laravelAdmin) {
                $laravelAdmin = User::create([
                    'name' => 'Simple Admin',
                    'email' => $this->adminEmail,
                    'password' => bcrypt('password'),
                    'role' => 'gov_admin',
                ]);
            }
            Auth::login($laravelAdmin);

            // Regenerate session ID to avoid session fixation and ensure middleware sees the login
            $request->session()->regenerate();

            return redirect('/admin/panel')->with('success', 'Logged in as admin.');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        // Log out both the simple admin session and the Laravel auth user.
        Auth::logout();
        $request->session()->forget(['admin_authenticated', 'admin_email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out.');
    }

    public function panel()
    {
        if (!session('admin_authenticated')) {
            return redirect('/admin/login');
        }

        return view('admin.panel');
    }
}
