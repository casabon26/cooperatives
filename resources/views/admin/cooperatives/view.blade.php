@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => route('admin.cooperatives.index'), 'label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">Cooperatives (View)</h5>
                        <div>
                            <a href="{{ route('admin.cooperatives.index') }}" class="btn btn-sm btn-primary me-2" target="_self">Manage Cooperatives</a>
                            
                        </div>
                    </div>

                    <p class="text-muted small">This is a read-only overview of cooperatives for administrators.</p>

                    <style>
                        /* Styled open-link button for cooperatives overview */
                        .open-link-btn {
                            display: inline-flex;
                            align-items: center;
                            gap: .4rem;
                            padding: .25rem .5rem;
                            border-radius: .35rem;
                            border: 1px solid rgba(239,68,68,0.65);
                            color: var(--danger);
                            background: transparent;
                            font-weight:600;
                            transition: background-color .12s ease, color .12s ease, transform .12s ease, box-shadow .12s ease;
                            text-decoration: none;
                        }
                        .open-link-btn:hover, .open-link-btn:focus {
                            background: var(--danger);
                            color: #fff;
                            text-decoration: none;
                            transform: translateY(-2px);
                            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
                        }
                    </style>

                    <div class="list-group">
                        @foreach($cooperatives as $coop)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $coop->name }}</h6>
                                    <small class="text-muted">{{ $coop->region ?? '—' }}</small>
                                </div>
                                <p class="mb-1 text-truncate">{{ $coop->description }}</p>
                                <small>
                                    <span class="badge bg-secondary">{{ $coop->sector ?? '—' }}</span>
                                    @if($coop->link)
                                        <a href="{{ $coop->link }}" target="_blank" rel="noopener" class="ms-2 open-link-btn">Open link</a>
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
