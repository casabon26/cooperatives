@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Register</h5>
                <form method="POST" action="/register" novalidate>
                    @csrf
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birthday</label>
                            <input type="date" id="birthday" name="birthday" class="form-control" value="{{ old('birthday') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" id="age" name="age" class="form-control" value="{{ old('age') }}" min="0" max="150">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Male" {{ old('sex')=='Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex')=='Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CP Number</label>
                        <input type="text" name="cp_number" class="form-control" value="{{ old('cp_number') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button class="btn btn-primary">Register</button>
                </form>

                <script>
                    // Auto-calc age from birthday
                    (function(){
                        var b = document.getElementById('birthday');
                        var age = document.getElementById('age');
                        if(!b || !age) return;
                        function calc(){
                            var v = b.value; if(!v) return;
                            var bd = new Date(v);
                            var today = new Date();
                            var a = today.getFullYear() - bd.getFullYear();
                            var m = today.getMonth() - bd.getMonth();
                            if (m < 0 || (m === 0 && today.getDate() < bd.getDate())) a--;
                            if(!isNaN(a)) age.value = a;
                        }
                        b.addEventListener('change', calc);
                        window.addEventListener('load', calc);
                    })();
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
