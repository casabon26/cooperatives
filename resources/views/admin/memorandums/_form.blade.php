<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $memorandum->title ?? '') }}">
    @error('title')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Published Year</label>
    @php
        $publishedYear = old('published_year');
        if (empty($publishedYear) && !empty($memorandum->published_at)) {
            try {
                if($memorandum->published_at instanceof \DateTimeInterface){
                    $publishedYear = $memorandum->published_at->format('Y');
                } else {
                    $publishedYear = \Carbon\Carbon::parse($memorandum->published_at)->format('Y');
                }
            } catch (\Throwable $e) {
                $publishedYear = '';
            }
        }
        $minYear = 1900;
        $maxYear = date('Y') + 5;
    @endphp
    <input type="text" name="published_year" class="form-control @error('published_year') is-invalid @enderror" value="{{ $publishedYear }}" inputmode="numeric" pattern="\d{4}" maxlength="4" placeholder="YYYY" title="Enter a 4-digit year (e.g. 2024)">
    @error('published_year')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Document (PDF or other)</label>
    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
    @error('file')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if(!empty($memorandum->file_path))
            <div class="mt-2 small">
            Current file: <a href="{{ asset('storage/'.$memorandum->file_path) }}">View</a>
        </div>
    @endif
</div>
