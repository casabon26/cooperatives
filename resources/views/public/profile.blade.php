@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">{{ $cooperative->name }}</h1>
    <p class="text-muted">{{ $cooperative->sector }} · {{ $cooperative->region }}</p>
    <section>
        <h2 class="h6">About</h2>
        <p>{{ $cooperative->description }}</p>
    </section>

    @if($cooperative->profile)
    <section>
        <h2 class="h6">Services</h2>
        <p>{{ $cooperative->profile->services }}</p>
        <h2 class="h6">Contact</h2>
        <p>{{ $cooperative->profile->contact_info }}</p>
    </section>
    @endif

    @if($cooperative->documents->count())
    <section>
        <h2 class="h6">Documents</h2>
        <ul>
            @foreach($cooperative->documents as $doc)
                <li><a href="/storage/{{ $doc->file_path }}">{{ $doc->document_type ?? 'Document' }}</a></li>
            @endforeach
        </ul>
    </section>
    @endif
</div>
@endsection
