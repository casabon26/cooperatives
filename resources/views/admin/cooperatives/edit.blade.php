@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">Edit Cooperative</h1>
    <form method="post" action="{{ route('admin.cooperatives.update',$cooperative) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name',$cooperative->name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Sector</label>
            <input name="sector" class="form-control" value="{{ old('sector',$cooperative->sector) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Region</label>
            <input name="region" class="form-control" value="{{ old('region',$cooperative->region) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Link (optional)</label>
            <input name="link" class="form-control" value="{{ old('link', $cooperative->link) }}" placeholder="https://example.com/coop-profile">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending','active','suspended','archived'] as $s)
                    <option value="{{ $s }}" {{ $cooperative->status===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
    
    <h2 class="h5 mt-4">Members</h2>
    <ul class="list-unstyled">
        @foreach($cooperative->users as $member)
            <li class="d-flex justify-content-between align-items-center py-1">
                <div>{{ $member->name }} <small class="text-muted">({{ $member->pivot->role }})</small></div>
                <form method="post" action="{{ route('admin.cooperatives.members.destroy', [$cooperative, $member]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                </form>
            </li>
        @endforeach
    </ul>

    <form method="post" action="{{ route('admin.cooperatives.members.store', $cooperative) }}" class="row g-2 mt-3">
        @csrf
        <div class="col-md-6"><input name="email" class="form-control" placeholder="User email to add" required></div>
        <div class="col-md-4"><select name="role" class="form-select"><option value="member">Member</option><option value="admin">Admin</option></select></div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Add</button></div>
    </form>
</div>
@endsection
