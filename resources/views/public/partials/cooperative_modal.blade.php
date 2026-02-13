<div class="coop-modal">
    <h2 class="h5 mb-1">{{ $cooperative->name }}</h2>
    <p class="small text-muted mb-2">{{ $cooperative->sector }} · {{ $cooperative->region }}</p>
    @if($cooperative->description)
        <p class="mb-2">{{ $cooperative->description }}</p>
    @endif

    @if($cooperative->profile)
        <div class="mb-2">
            @if($cooperative->profile->contact_person)
                <div><strong>Contact:</strong> {{ $cooperative->profile->contact_person }}</div>
            @endif
            @if($cooperative->profile->contact_email)
                <div><strong>Email:</strong> <a href="mailto:{{ $cooperative->profile->contact_email }}">{{ $cooperative->profile->contact_email }}</a></div>
            @endif
            @if($cooperative->profile->address)
                <div><strong>Address:</strong> {{ $cooperative->profile->address }}</div>
            @endif
        </div>
    @endif

    @if($cooperative->documents && $cooperative->documents->count())
        <hr>
        <h6 class="mb-2">Documents</h6>
        <ul class="list-unstyled small mb-0">
            @foreach($cooperative->documents as $doc)
                @php
                    $fileUrl = null;
                    if ($doc->file_path) {
                        $publicPath = public_path($doc->file_path);
                        $storagePath = public_path('storage/'.$doc->file_path);
                        if (file_exists($publicPath)) {
                            $fileUrl = asset($doc->file_path);
                        } elseif (file_exists($storagePath)) {
                            $fileUrl = asset('storage/'.$doc->file_path);
                        }
                    }
                @endphp
                <li>
                    @if($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank">{{ $doc->document_type ?? 'Document' }}</a>
                    @else
                        <span class="text-muted">{{ $doc->document_type ?? 'Document' }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <div class="mt-3">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('cooperatives.profile', $cooperative) }}">View full profile</a>
    </div>
</div>