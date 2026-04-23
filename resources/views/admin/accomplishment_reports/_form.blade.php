<div class="mb-3">
    <label class="form-label">Code</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $accomplishmentReport->code ?? $code ?? '') }}" @if(isset($code)) readonly @endif>
</div>

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $accomplishmentReport->title ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Content</label>
    <textarea name="content" class="form-control" rows="6">{{ old('content', $accomplishmentReport->content ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Published At</label>
    @php
        $publishedVal = '';
        $old = old('published_at');
        if(!empty($old)){
            $publishedVal = $old;
        } elseif(!empty($accomplishmentReport->published_at)) {
            try {
                if($accomplishmentReport->published_at instanceof \DateTimeInterface){
                    $publishedVal = $accomplishmentReport->published_at->format('Y-m-d\TH:i');
                } else {
                    $publishedVal = \Carbon\Carbon::parse($accomplishmentReport->published_at)->format('Y-m-d\TH:i');
                }
            } catch (\Throwable $e) {
                $publishedVal = (string) $accomplishmentReport->published_at;
            }
        }
    @endphp
    <input type="datetime-local" name="published_at" class="form-control" value="{{ $publishedVal }}">
</div>

<div class="mb-3">
    <label class="form-label">Document (PDF or other)</label>
    <input type="file" name="file" class="form-control">
    @if(!empty($accomplishmentReport->file_path))
            <div class="mt-2 small">
            Current file: <a href="{{ asset('storage/'.$accomplishmentReport->file_path) }}">View</a>
        </div>
    @endif
</div>
