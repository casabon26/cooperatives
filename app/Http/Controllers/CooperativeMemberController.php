<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CooperativeMemberController extends Controller
{
    public function store(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);

        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'nullable|string',
        ]);

        $user = User::where('email',$data['email'])->first();
        if (!$user) return back()->withErrors(['email'=>'User not found']);

        // attach if not exists
        if (!$cooperative->users()->where('user_id',$user->id)->exists()) {
            $cooperative->users()->attach($user->id, ['role'=>$data['role'] ?? 'member']);
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'add_member',
                    'ip_address' => $request->ip(),
                    'meta' => ['cooperative_id'=>$cooperative->id,'added_user_id'=>$user->id],
                ]);
            } catch (\Throwable $e) {}
        }

        return back()->with('success','Member added');
    }

    public function destroy(Cooperative $cooperative, User $user)
    {
        $this->authorize('manage', $cooperative);
        $cooperative->users()->detach($user->id);
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'remove_member',
                'ip_address' => request()->ip(),
                'meta' => ['cooperative_id'=>$cooperative->id,'removed_user_id'=>$user->id],
            ]);
        } catch (\Throwable $e) {}
        return back()->with('success','Member removed');
    }
}
