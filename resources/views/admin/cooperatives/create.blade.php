@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Create Cooperative</h4>
                <div>
                    <form method="POST" action="{{ route('admin.cooperatives.import_default') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm me-2">Import Default 34</button>
                    </form>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="history.back(); return false;">Back</a>
                </div>
            </div>
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cooperatives.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sector</label>
                            <input name="sector" class="form-control" value="{{ old('sector') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Region</label>
                            <input name="region" class="form-control" value="{{ old('region') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link (optional)</label>
                            <input name="link" class="form-control" value="{{ old('link') }}" placeholder="https://example.com/coop-profile">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">active</option>
                                <option value="pending">pending</option>
                                <option value="suspended">suspended</option>
                                <option value="archived">archived</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
