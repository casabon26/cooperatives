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
    <label class="form-label">File (PDF / PPT)</label>
    <input type="file" name="file" class="form-control">
    @if(!empty($resource->file_path))
        <div class="small mt-2">Current: <a href="{{ asset('storage/'.$resource->file_path) }}" target="_blank">Open file</a></div>
    @endif
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('admin.cooperative-resources.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
