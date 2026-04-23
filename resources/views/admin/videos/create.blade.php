@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Add Video</div>
                <div class="card-body">
                    <form action="{{ url('/admin/manage-videos') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Video URL (YouTube/Vimeo or direct link)</label>
                            <input type="url" name="url" class="form-control" placeholder="https://..." value="{{ old('url') }}">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="highlight_landing" name="highlight_landing" {{ old('highlight_landing') ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_landing">Highlight on landing page</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="highlight_enterprise" name="highlight_enterprise" {{ old('highlight_enterprise') ? 'checked' : '' }}>
                            <label class="form-check-label" for="highlight_enterprise">Highlight on Enterprise Portal</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Upload Video (mp4, webm, ogg)</label>
                            <input type="file" name="file" accept="video/mp4,video/webm,video/ogg" class="form-control">
                            @if($errors->first('file'))
                                <div class="form-text text-danger">{{ $errors->first('file') }}</div>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Add Video</button>
                            @include('partials.back-button', ['url' => url('/admin/manage-videos'), 'label' => 'Cancel', 'class' => 'btn-secondary'])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
