@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => url('/admin/manage-training'), 'label' => 'Back'])
@endsection

@section('content')
<div class="py-4">
    <h3>Edit Training Video</h3>
    <form method="POST" action="/admin/manage-training/{{ $video->id }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" value="{{ $video->title }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ $video->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">YouTube URL (optional)</label>
            <input name="url" class="form-control" value="{{ $video->url }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Upload File (mp4/webm/ogg)</label>
            <input type="file" name="file" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Video Length (seconds)</label>
            <input type="number" name="length" class="form-control" min="1" step="1" value="{{ $video->length }}" placeholder="e.g. 300">
        </div>
        
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
