@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Memorandum Circulars</h5>
                @if($memorandums->count())
                    <ul class="list-unstyled">
                        @foreach($memorandums as $memo)
                            <li class="py-2 border-bottom">
                                <a href="{{ url('/memorandums/'.$memo->id) }}" class="h6 d-block text-decoration-none" onclick="window.location.href=this.href; return false;">{{ $memo->code ?? $memo->title }}</a>
                                @if($memo->title)
                                    <div class="small text-muted">{{ $memo->title }}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3">{{ $memorandums->links() }}</div>
                @else
                    <div class="small text-muted">No memorandum circulars found.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
