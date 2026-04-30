@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 col-md-3">
            <!-- Sidebar Navigation -->
            <div class="list-group mb-4">
                <a href="{{ route('admin.profile.show') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(var(--primary-r), 0.1); color: var(--primary-dark); transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(184,33,50,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Profile Information
                </a>
                <a href="{{ route('admin.profile.edit') }}" target="_self" class="list-group-item list-group-item-action active" aria-current="true" style="background: linear-gradient(135deg, rgba(185,28,28,0.12), rgba(239,68,68,0.08)); border-color: rgba(185,28,28,0.2); color: #7f1d1d;">
                    <strong>Edit Profile</strong>
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
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validation Errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $profile->name ?? $user->name) }}" 
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $profile->email ?? $user->email) }}" 
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input 
                                type="tel" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone', $profile->phone ?? '') }}" 
                                placeholder="+1 (555) 000-0000"
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio (Optional)</label>
                            <textarea 
                                class="form-control @error('bio') is-invalid @enderror" 
                                id="bio" 
                                name="bio" 
                                rows="4" 
                                placeholder="Tell us about yourself..."
                                maxlength="500"
                            >{{ old('bio', $profile->bio ?? '') }}</textarea>
                            <small class="form-text text-muted">Maximum 500 characters</small>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
