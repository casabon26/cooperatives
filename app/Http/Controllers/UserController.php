<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\TrainingCompletion;
use App\Models\Video;

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

    public function certificates(User $user)
    {
        $this->authorize('access-admin');
        $certs = \App\Models\TrainingCompletion::with('video')->where('user_id', $user->id)->orderByDesc('completed_at')->get();
        return view('admin.users.certificates', compact('user','certs'));
    }

    public function certificateOpen(User $user, Video $video)
    {
        $this->authorize('access-admin');
        $tc = TrainingCompletion::where('user_id', $user->id)->where('video_id', $video->id)->firstOrFail();
        // Show print-only certificate view for admin
        return view('training.certificate_print', compact('video','tc','user'));
    }

    public function certificateDownload(User $user, Video $video)
    {
        $this->authorize('access-admin');
        $tc = TrainingCompletion::where('user_id', $user->id)->where('video_id', $video->id)->firstOrFail();

        // Render print-only certificate HTML (only the certificate content)
        $html = view('training.certificate_print', compact('video','tc','user'))->render();

        // Try using Barryvdh Snappy / PDF facade if available
        if (class_exists(\Barryvdh\Snappy\Facades\SnappyPdf::class)) {
            try {
                return \Barryvdh\Snappy\Facades\SnappyPdf::loadHTML($html)->setPaper('a4')->download('certificate-' . ($tc->id ?? $tc->video_id) . '.pdf');
            } catch (\Throwable $e) {
                // fallthrough to binary
            }
        }

        if (class_exists('PDF')) {
            try {
                return \PDF::loadHTML($html)->setPaper('a4')->download('certificate-' . ($tc->id ?? $tc->video_id) . '.pdf');
            } catch (\Throwable $e) {
                // fallthrough
            }
        }

        // Fallback: try calling wkhtmltopdf binary directly
        $binary = env('WKHTMLTOPDF_BINARY', 'wkhtmltopdf');
        $cmd = escapeshellcmd($binary) . ' -q - -';

        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($cmd, $descriptors, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $html);
            fclose($pipes[0]);

            $pdf = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $status = proc_close($process);
            if ($status === 0 && $pdf !== null && strlen($pdf) > 10) {
                return response($pdf, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="certificate-' . ($tc->id ?? $tc->video_id) . '.pdf"',
                ]);
            }
        }

        // As a last resort, return HTML view
        return view('training.certificate', compact('video','tc','user'));
    }
}
