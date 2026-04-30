@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Site Settings</div>

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf

                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        {{-- General --}}
                        <h5 class="mb-2">General</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Site Name</label>
                                <input name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tagline</label>
                                <input name="tagline" class="form-control" value="{{ old('tagline', $settings['tagline'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Logo</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="logo" class="form-control-file">
                                    @if(!empty($settings['logo']))
                                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="logo" style="height:48px; margin-left:12px; border-radius:6px; object-fit:contain; background:#fff; padding:6px; border:1px solid rgba(0,0,0,0.06)">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Branding --}}
                        <h5 class="mb-2">Branding</h5>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Primary Color</label>
                                <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $settings['primary_color'] ?? '#B82132') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Accent Color</label>
                                <input type="color" name="accent_color" class="form-control form-control-color" value="{{ old('accent_color', $settings['accent_color'] ?? '#fca5a5') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" name="per_page" class="form-control" value="{{ old('per_page', $settings['per_page'] ?? 10) }}">
                            </div>
                        </div>

                        {{-- Contact --}}
                        <h5 class="mb-2">Contact</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Phone</label>
                                <input name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Address</label>
                                <input name="address" class="form-control" value="{{ old('address', $settings['address'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Social --}}
                        <h5 class="mb-2">Social</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Facebook URL</label>
                                <input name="facebook" class="form-control" value="{{ old('facebook', $settings['facebook'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Twitter URL</label>
                                <input name="twitter" class="form-control" value="{{ old('twitter', $settings['twitter'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Advanced --}}
                        <h5 class="mb-2">Advanced</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Google Analytics ID</label>
                                <input name="google_analytics_id" class="form-control" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Footer Text</label>
                                <input name="footer_text" class="form-control" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Maintenance Mode</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" {{ (old('maintenance_mode', $settings['maintenance_mode'] ?? '0') == '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">Enable maintenance mode</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
