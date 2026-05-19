@extends('layouts.app')

@section('hero')
    <div class="py-5 text-center bg-white mb-4">
        <div class="container">
            <h1 class="display-6">Edit Profile</h1>
            <p class="text-muted">Update your personal information</p>
        </div>
    </div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Display name</label>
                    <input name="name" class="form-control" value="{{ old('name', $user->name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact number</label>
                    <input name="cp_number" class="form-control" value="{{ old('cp_number', $user->cp_number) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input name="address" class="form-control" value="{{ old('address', $user->address) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-select">
                        <option value="">Select</option>
                        <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Birthday</label>
                    <input name="birthday" type="date" class="form-control" value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->toDateString() : '') }}">
                </div>

                <hr>
                <h6>Change password</h6>
                <div class="mb-3">
                    <label class="form-label">New password</label>
                    <input name="password" type="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input name="password_confirmation" type="password" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Save changes</button>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Back to profile</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
