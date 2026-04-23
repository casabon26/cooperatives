@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">Create Admin/User</h1>
    <form method="post" action="{{ route('admin.users.store') }}" style="max-width:600px">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input name="password_confirmation" type="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="gov_admin">Admin (Admin panel access)</option>
                <option value="public">Public</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Create</button>
            @include('partials.back-button', ['url' => route('admin.users.index'), 'label' => 'Cancel', 'class' => 'btn-secondary'])
        </div>
    </form>
</div>
@endsection
