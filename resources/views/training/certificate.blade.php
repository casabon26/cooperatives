@extends('layouts.app')

@section('content')
<div class="py-4 text-center">
    <h2>Certificate of Completion</h2>
    <p class="lead">This certifies that <strong>{{ $user->name }}</strong> has completed the training: <strong>{{ $video->title }}</strong></p>
    <p>Date: {{ $tc->completed_at->format('F j, Y') }}</p>

    <div class="mt-4">
        <a href="#" onclick="window.print();return false;" class="btn btn-primary">Print / Save PDF</a>
    </div>
</div>
@endsection
