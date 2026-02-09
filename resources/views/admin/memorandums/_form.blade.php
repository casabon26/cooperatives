<div class="mb-3">
    <label class="form-label">Code</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $memorandum->code ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $memorandum->title ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Content</label>
    <textarea name="content" class="form-control" rows="6">{{ old('content', $memorandum->content ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Published At</label>
    @php
        $publishedVal = '';
        $old = old('published_at');
        if(!empty($old)){
            $publishedVal = $old;
        } elseif(!empty($memorandum->published_at)) {
            try {
                if($memorandum->published_at instanceof \DateTimeInterface){
                    $publishedVal = $memorandum->published_at->format('Y-m-d\TH:i');
                } else {
                    $publishedVal = \Carbon\Carbon::parse($memorandum->published_at)->format('Y-m-d\TH:i');
                }
            } catch (\Throwable $e) {
                $publishedVal = (string) $memorandum->published_at;
            }
        }
    @endphp
    <input type="datetime-local" name="published_at" class="form-control" value="{{ $publishedVal }}">
</div>

<div class="mb-3">
    <label class="form-label">Document (PDF or other)</label>
    <input type="file" name="file" class="form-control">
    @if(!empty($memorandum->file_path))
            <div class="mt-2 small">
            Current file: <a href="{{ asset('storage/'.$memorandum->file_path) }}">View</a>
        </div>
    @endif
</div>
