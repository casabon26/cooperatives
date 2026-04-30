@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 col-md-3">
            <!-- Sidebar Navigation -->
            <div class="list-group mb-4">
                <a href="{{ route('admin.profile.show') }}" target="_self" class="list-group-item list-group-item-action active" aria-current="true" style="background: linear-gradient(135deg, rgba(185,28,28,0.12), rgba(239,68,68,0.08)); border-color: rgba(185,28,28,0.2); color: #7f1d1d;">
                    <strong>Profile Information</strong>
                </a>
                <a href="{{ route('admin.profile.edit') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(185,28,28,0.1); color: #7f1d1d; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(185,28,28,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Edit Profile
                </a>
                <a href="{{ route('admin.profile.change-password') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(185,28,28,0.1); color: #7f1d1d; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(185,28,28,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Change Password
                </a>
                <a href="{{ url('/admin/panel') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(var(--primary-r), 0.1); color: var(--primary); font-weight: 600; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(184,33,50,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="col-12 col-md-9">
            <div class="card" style="border: 1px solid rgba(var(--primary-r), 0.12); border-radius: 10px; box-shadow: 0 4px 12px rgba(var(--primary-r), 0.06);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(var(--primary-r), 0.08) 0%, rgba(var(--primary-r), 0.04) 100%); border-bottom: 1px solid rgba(var(--primary-r), 0.1); padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, rgba(var(--primary-r), 0.12), rgba(var(--primary-r), 0.08)); display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 8a3 3 0 100-6 3 3 0 000 6z" stroke="#B82132" stroke-width="1.2" fill="none"/>
                                <path d="M2 14s1.5-3 6-3 6 3 6 3" stroke="#B82132" stroke-width="1.2" fill="none" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h5 class="mb-0" style="color: var(--primary-dark); font-weight: 700;">Admin Profile</h5>
                    </div>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #22c55e;">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Name</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $profile->name ?? $user->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $profile->email ?? $user->email }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Phone</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $profile->phone ?? 'Not provided' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Role</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <span class="badge" style="background: linear-gradient(135deg, rgba(var(--primary-r), 0.15), rgba(var(--primary-r), 0.1)); color: var(--primary); font-weight: 600; padding: 6px 10px;">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Bio</label>
                        <div style="padding: 12px 14px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2); min-height: 80px;">
                            <p style="color: #4b5563; margin: 0;">{{ $profile->bio ?? 'No bio added yet.' }}</p>
                        </div>
                    </div>

                    <hr style="border-color: rgba(185,28,28,0.1); margin: 1.5rem 0;">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Member Since</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $user->created_at->format('F j, Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Last Updated</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $profile->updated_at->format('F j, Y \a\t g:i A') ?? 'Never' }}</strong>
                            </div>
                        </div>
                    </div>

                    @if($profile->password_changed_at)
                        <div class="mt-4">
                            <label class="form-label" style="color: #7f1d1d; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Password Last Changed</label>
                            <div style="padding: 10px 12px; background: rgba(var(--primary-r), 0.03); border-radius: 6px; border-left: 3px solid rgba(var(--primary-r), 0.2);">
                                <strong style="color: #1f2937;">{{ $profile->password_changed_at->format('F j, Y \a\t g:i A') }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
