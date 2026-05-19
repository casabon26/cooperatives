@extends('layouts.app')

@section('hero')
    <div class="py-5 text-center bg-white mb-4">
        <div class="container">
            <h1 class="display-6">My Profile</h1>
            <p class="text-muted">Your account information and credentials</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-3">Account</h3>

                <dl class="row">
                    <dt class="col-sm-4">Full name</dt>
                    <dd class="col-sm-8">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4">Role</dt>
                    <dd class="col-sm-8">{{ $user->role ?? 'user' }}</dd>

                    <dt class="col-sm-4">Contact</dt>
                    <dd class="col-sm-8">{{ $user->cp_number ?? '-' }}</dd>

                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $user->address ?? '-' }}</dd>

                    <dt class="col-sm-4">Sex</dt>
                    <dd class="col-sm-8">{{ $user->sex ?? '-' }}</dd>

                    <dt class="col-sm-4">Age</dt>
                    <dd class="col-sm-8">{{ $user->age ?? '-' }}</dd>

                    <dt class="col-sm-4">Birthday</dt>
                    <dd class="col-sm-8">{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->toFormattedDateString() : '-' }}</dd>

                    <dt class="col-sm-4">Member since</dt>
                    <dd class="col-sm-8">{{ $user->created_at ? $user->created_at->toDayDateTimeString() : '-' }}</dd>

                    <dt class="col-sm-4">Last updated</dt>
                    <dd class="col-sm-8">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</dd>
                </dl>

                <div class="mt-3">
                    <!-- Account settings removed per request -->
                    @if(!in_array($user->role ?? 'user', ['gov_admin','cooperative_admin','admin']))
                        <a href="/profile/certificates" class="btn btn-outline-secondary">My certificates</a>
                    @endif
                </div>
            </div>

            @if($user->cooperatives && $user->cooperatives->count())
                <div class="card p-4 mt-3">
                    <h4>Cooperative memberships</h4>
                    <ul class="list-group list-group-flush mt-2">
                        @foreach($user->cooperatives as $coop)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $coop->name }}</strong>
                                    <div class="text-muted small">{{ $coop->municipality ?? '' }} {{ $coop->province ?? '' }}</div>
                                </div>
                                <span class="badge bg-secondary">{{ $coop->pivot->role ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="mb-3">
                    <div style="width:96px;height:96px;margin:0 auto;border-radius:999px;background:linear-gradient(135deg,#fff,#fee2e2);display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--primary);font-size:28px;">{{ strtoupper(substr($user->first_name ?? $user->name,0,1)) }}</div>
                </div>
                <h5 class="mb-0">{{ $user->first_name ? ($user->first_name . ' ' . ($user->last_name ?? '')) : $user->name }}</h5>
                <p class="text-muted small">{{ $user->email }}</p>

                <div class="d-grid gap-2">
                    <a href="/profile/edit" class="btn btn-primary">Edit profile</a>
                    <!-- Account settings removed per request -->
                </div>
            </div>

            <div class="card p-4 mt-3">
                <h6>Quick actions</h6>
                <div class="list-group list-group-flush mt-2">
                    <a href="/logout" onclick="event.preventDefault();document.getElementById('logoutForm').submit();" class="list-group-item list-group-item-action">Logout</a>
                </div>
                <form id="logoutForm" method="POST" action="/logout">@csrf</form>
            </div>
        </div>
    </div>
@endsection
