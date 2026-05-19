@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">Certificates — {{ $user->name }}</h1>
    @if($certs->count())
        <div class="row g-3">
            @foreach($certs as $c)
                <div class="col-md-4">
                    <div class="card p-3 h-100">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:96px;height:64px;border-radius:6px;background:linear-gradient(135deg,#fff0f6,#ffe6f0);display:flex;align-items:center;justify-content:center;font-weight:700;color:#b91c1c;">
                                <div style="text-align:center;font-size:12px">CERT<br>#{!! str_pad($c->id ?? $c->video_id,3,'0',STR_PAD_LEFT) !!}</div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $c->video->title ?? 'Training' }}</h6>
                                <div class="small text-muted">Completed: {{ $c->completed_at ? $c->completed_at->toDayDateTimeString() : '-' }}</div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            @if(!empty($c->certificate_token) && !empty($c->video))
                                <a href="{{ route('admin.users.certificates.print', [$user, $c->video]) }}?print=1" target="_blank" class="btn btn-sm btn-primary">Print</a>
                                <a href="{{ route('admin.users.certificates.download', [$user, $c->video]) }}" class="btn btn-sm btn-outline-secondary">Download PDF</a>
                            @endif
                            <div class="flex-grow-1"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-4">
            <div class="small text-muted">This user has no certificates.</div>
        </div>
    @endif
</div>
@endsection
