@csrf

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $resource->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $resource->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Google Drive link (optional)</label>
    <input type="url" name="gdrive_link" class="form-control" value="{{ old('gdrive_link', $resource->gdrive_link ?? '') }}" placeholder="https://drive.google.com/...">
    @if(!empty($resource->gdrive_link))
        <div class="small mt-2">Current: <a href="{{ $resource->gdrive_link }}" target="_blank">Open Drive link</a></div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">File (PDF / PPT)</label>
    <input type="file" name="file" class="form-control">
    @if(!empty($resource->file_path))
        <div class="small mt-2">Current: <a href="{{ asset('storage/'.$resource->file_path) }}" target="_blank">Open file</a></div>
    @endif
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary">Save</button>
    @include('partials.back-button', ['url' => isset($cooperative) ? route('cooperatives.profile', $cooperative) : route('admin.cooperative-resources.index'), 'label' => 'Cancel', 'class' => 'btn-outline-secondary'])
</div>
