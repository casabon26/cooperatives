<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(40);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('access-admin');
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:public,cooperative_admin,gov_admin',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        try { AuditLog::create(['user_id'=>$request->user()->id,'action'=>'create_user','ip_address'=>$request->ip(),'meta'=>['user_id'=>$user->id,'role'=>$user->role]]); } catch (\Throwable $e) {}

        return redirect()->route('admin.users.index')->with('success','User created');
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('access-admin');
        $data = $request->validate(['role'=>'required|in:public,cooperative_admin,gov_admin']);
        $user->role = $data['role'];
        $user->save();
        try { AuditLog::create(['user_id'=>$request->user()->id,'action'=>'update_user_role','ip_address'=>$request->ip(),'meta'=>['user_id'=>$user->id,'role'=>$user->role]]); } catch (\Throwable $e) {}
        return back()->with('success','Role updated');
    }
}
