@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h4>Edit Video</h4>
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
