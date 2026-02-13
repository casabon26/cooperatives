@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Enterprise</h4>
        <div>
            <a href="{{ route('admin.enterprises.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="enterprise-form" method="POST" action="{{ route('admin.enterprises.update', $enterprise->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $enterprise->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Micro" {{ old('category', $enterprise->category) == 'Micro' ? 'selected' : '' }}>Micro</option>
                        <option value="Small" {{ old('category', $enterprise->category) == 'Small' ? 'selected' : '' }}>Small</option>
                        <option value="Medium" {{ old('category', $enterprise->category) == 'Medium' ? 'selected' : '' }}>Medium</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <input type="text" name="summary" class="form-control" value="{{ old('summary', $enterprise->summary) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $enterprise->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image (optional)</label>
                    @if(!empty($enterprise->image_url))
                        <div class="mb-2"><img src="{{ $enterprise->image_url }}" alt="" style="max-width:140px; max-height:90px; object-fit:cover; border-radius:6px"></div>
                    @endif
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button class="btn btn-primary">Save</button>
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
