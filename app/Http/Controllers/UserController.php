<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(40);
        return view('admin.users.index', compact('users'));
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
