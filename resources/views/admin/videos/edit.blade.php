@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => '/admin/manage-videos', 'label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Edit Video</h4>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="/admin/manage-videos/{{ $video->id }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" class="form-control" value="{{ $video->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="3">{{ $video->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Video URL (YouTube/Vimeo or direct link)</label>
                            <input name="url" class="form-control" value="{{ $video->url }}" placeholder="https://...">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="highlight_landing" id="highlight_landing" value="1" class="form-check-input" {{ $video->highlight_landing ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_landing">Highlight on landing page</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="highlight_enterprise" id="highlight_enterprise" value="1" class="form-check-input" {{ $video->highlight_enterprise ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_enterprise">Highlight on Enterprise Portal</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Upload Video (mp4, webm)</label>
                            <input type="file" name="file" accept="video/*" class="form-control">
                        </div>
                        <button class="btn btn-danger">Update Video</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
