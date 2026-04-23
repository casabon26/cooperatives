@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => route('admin.enterprises.index'), 'label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Create Enterprise</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="enterprise-form" method="POST" action="{{ route('admin.enterprises.store') }}" enctype="multipart/form-data" target="_self">
                @csrf
                <div class="mb-3">
                    <label class="form-label">BUSINESS NAME</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">BUSINESS ADDRESS</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Industry/Line</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">SIZE</label>
                    <select name="category" class="form-select" required>
                        <option value="Micro">Micro</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-accent">Create</button>
            </form>
        </div>
    </div>

    <!-- Loading overlay for uploads -->
    <div id="enterprise-upload-loading" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.85);z-index:1050;align-items:center;justify-content:center">
        <div style="text-align:center">
            <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem"></div>
            <div class="mt-2">Uploading image... please wait</div>
        </div>
    </div>

@section('scripts')
    <script>
        (function(){
            const form = document.getElementById('enterprise-form');
            if(!form) return;
            const overlay = document.getElementById('enterprise-upload-loading');
            const submit = form.querySelector('button[type="submit"], button');

            form.addEventListener('submit', function(){
                if(overlay) overlay.style.display = 'flex';
                if(submit) submit.disabled = true;
            });

            window.addEventListener('load', function(){
                const hasFlash = document.querySelector('.alert');
                if(hasFlash && overlay) overlay.style.display = 'none';
            });
        })();
    </script>
@endsection

</div>
@endsection
