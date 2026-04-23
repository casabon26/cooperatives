@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => url('/admin/manage-training'), 'label' => 'Back'])
@endsection

@section('content')
<div class="py-4">
    <h3>Add Training Video</h3>
    <form method="POST" action="/admin/manage-training" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">YouTube URL (optional)</label>
            <input name="url" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Upload File (mp4/webm/ogg)</label>
            <input type="file" name="file" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Video Length (seconds)</label>
            <input type="number" name="length" class="form-control" min="1" step="1" placeholder="e.g. 300">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
