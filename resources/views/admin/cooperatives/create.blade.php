@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['label' => 'Back', 'class' => 'btn-sm'])
@endsection

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
                        
                        <h5 class="mb-2">Profile / Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Mission</label>
                            <textarea name="mission" class="form-control" rows="3">{{ old('mission') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vision</label>
                            <textarea name="vision" class="form-control" rows="3">{{ old('vision') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">About / Objectives</label>
                            <textarea name="objectives" class="form-control" rows="3">{{ old('objectives') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Services / Offerings</label>
                            <textarea name="services" class="form-control" rows="3">{{ old('services') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Achievements / Key Info</label>
                            <textarea name="achievements" class="form-control" rows="3">{{ old('achievements') }}</textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Years</label>
                                <input name="years" class="form-control" value="{{ old('years') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <input name="address" class="form-control" value="{{ old('address') }}">
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input name="contact_email" type="email" class="form-control" value="{{ old('contact_email') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gallery (upload images)</label>
                            <input type="file" name="gallery_files[]" multiple accept="image/*" class="form-control">
                            <div class="form-text">You may upload multiple images. Files will be stored and referenced by the profile.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Info</label>
                            <textarea name="contact_info" class="form-control" rows="3">{{ old('contact_info') }}</textarea>
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
