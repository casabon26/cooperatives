<div class="text-center py-3">
    <div class="mb-2">
        @if(!empty($slpa->image_url))
            <img src="{{ $slpa->image_url }}" alt="{{ $slpa->name }}" style="width:96px;height:96px;object-fit:cover;border-radius:50%;">
        @else
            <div style="width:96px;height:96px;border-radius:50%;background:#f8fafc;display:inline-flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3" stroke="#cbd5e1" stroke-width="1.2"/><path d="M4 20c0-3.314 4-5 8-5s8 1.686 8 5" stroke="#e2e8f0" stroke-width="1.2" stroke-linecap="round"/></svg>
            </div>
        @endif
    </div>

    <h4 class="fw-semibold mb-1">{{ $slpa->name }}</h4>
    <div class="small text-muted mb-3">
        SLPA @if(!empty($slpa->business)) &middot; {{ $slpa->business }}@endif
        @if(!empty($slpa->address)) &middot; {{ Str::limit($slpa->address,80) }}@endif
    </div>

    @if(!empty($slpa->description))
        <div class="mb-3 text-muted">{{ $slpa->description }}</div>
    @endif

    <div>
        <a href="{{ route('slpas.show', $slpa) }}" class="btn btn-outline-secondary">View full profile</a>
    </div>
</div>
