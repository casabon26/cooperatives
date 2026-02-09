@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Admin Login</h5>

                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="/admin/login">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email','admin@portal.local') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" value="{{ old('password') }}" required>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-danger">Login</button>
                        </div>
                    </form>

                    <div class="mt-3 small text-muted">Note: this login uses a hardcoded admin account.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
