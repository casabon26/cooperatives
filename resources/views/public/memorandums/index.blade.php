@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="mb-4">
            <a href="{{ url('/') }}" class="btn btn-outline-danger d-inline-flex align-items-center" role="button" aria-label="Back to home" title="Back to home" target="_self">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Home
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h2 class="card-title mb-2" style="font-weight:800; color:var(--primary); font-size:1.75rem;">Memorandum Circulars</h2>
                    <p class="text-muted mb-0">Official memorandum and circular releases from CCLDO Cabuyao</p>
                </div>

                @if($memorandums->count())
                    <div class="memo-list-view">
                        @foreach($memorandums as $memo)
                            <div class="memo-card mb-3" style="padding:1rem; border:1px solid rgba(var(--primary-r), 0.12); border-radius:10px; transition:all .2s ease; display:flex; gap:1rem; align-items:flex-start;">
                                <div class="memo-icon flex-shrink-0" style="width:40px; height:40px; border-radius:8px; background:rgba(var(--primary-r), 0.08); display:flex; align-items:center; justify-content:center;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 7h6v6H7z" stroke="#B82132" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 15V7a2 2 0 0 0-2-2H9" stroke="#B82132" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="{{ url('/memorandums/'.$memo->id) }}" class="h5 d-block text-decoration-none mb-1" style="color:var(--primary); font-weight:700; transition:all .2s ease;" onmouseover="this.style.color='#D2665A'" onmouseout="this.style.color='#B82132'">{{ $memo->title }}</a>
                                    <div class="small" style="color:#6b7280;">Published: <strong>{{ optional($memo->published_at ?? $memo->created_at)->toFormattedDateString() }}</strong></div>
                                </div>
                                <div class="flex-shrink-0" style="min-width:50px; text-align:right;">
                                    <span class="badge" style="background:linear-gradient(135deg,#fee2e2,#fdd2d2); color:#991b1b; font-weight:700; font-size:0.85rem;">{{ optional($memo->published_at ?? $memo->created_at)->format('Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $memorandums->links() }}</div>
                @else
                    <div class="alert alert-info" style="border-left:4px solid #2563eb; background:rgba(37,99,235,0.04);">
                        <strong>No memorandum circulars found.</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
