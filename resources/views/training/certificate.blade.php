@extends('layouts.app')

@section('styles')
    .cert-wrap { background: linear-gradient(135deg,#fff7f8,#fff1f2); padding:36px; border-radius:12px; border:1px solid rgba(200,16,46,0.06); position:relative; overflow:hidden; box-sizing:border-box; }
    /* corner decorations use inline SVG elements so they print reliably */
    .corner-svg { position: absolute; width: 240px; height: 240px; z-index: 0; pointer-events: none; }
    .corner-top-left { left: -60px; top: -60px; }
    .corner-bottom-right { right: -60px; bottom: -60px; }
    .cert-body { background:white; padding:28px; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.06); position:relative; z-index:1; }
    .cert-title { font-weight:800; color:#b91c1c; letter-spacing:1px; }

    /* Print styles: make certificate occupy entire page and hide other elements */
    @media print {
        html, body { height: 100%; margin: 0 !important; padding: 0 !important; }
        @page { size: A4; margin: 0; }
        /* hide everything by default */
        body * { visibility: hidden !important; }
        /* show only the certificate wrapper and its children */
        .cert-wrap, .cert-wrap * { visibility: visible !important; }
        /* expand certificate to fill printable area */
        .cert-wrap { position: fixed !important; left: 0 !important; top: 0 !important; width: 100% !important; height: 100% !important; max-width: none !important; border-radius: 0 !important; box-shadow: none !important; padding: 28mm !important; background: transparent !important; }
        .cert-body { width: 100% !important; height: 100% !important; padding: 0 !important; background: white !important; border-radius: 0 !important; box-shadow: none !important; }
        /* hide print button when printing */
        .no-print { display: none !important; }
    }
@endsection

@section('content')
<div class="py-4">
    <div class="cert-wrap mx-auto" style="max-width:900px;">
        <!-- decorative SVG corners (DOM elements print reliably) -->
        <svg class="corner-svg corner-top-left" viewBox="0 0 240 240" preserveAspectRatio="none" role="presentation" aria-hidden="true">
            <defs>
                <linearGradient id="gRed" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0%" stop-color="#dc2626" />
                    <stop offset="100%" stop-color="#fca5a5" />
                </linearGradient>
            </defs>
            <polygon points="0,0 240,0 0,240" fill="url(#gRed)" />
        </svg>
        <svg class="corner-svg corner-bottom-right" viewBox="0 0 240 240" preserveAspectRatio="none" role="presentation" aria-hidden="true">
            <defs>
                <linearGradient id="gBlue" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0%" stop-color="#0ea5e9" />
                    <stop offset="100%" stop-color="#60a5fa" />
                </linearGradient>
            </defs>
            <polygon points="240,240 0,240 240,0" fill="url(#gBlue)" />
        </svg>
        <div class="cert-body text-center">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="text-align:left">
                    <h3 class="cert-title">Certificate of Completion</h3>
                    <div class="small text-muted">Awarded to</div>
                </div>
                <div style="text-align:right">
                    <img src="{{ asset('assets/images/logo/CCLDO.png') }}" alt="logo" style="height:48px;object-fit:contain">
                </div>
            </div>

            <h2 style="margin-top:6px;margin-bottom:6px;font-weight:800">{{ $user->first_name ? ($user->first_name . ' ' . ($user->last_name ?? '')) : $user->name }}</h2>
            <div class="small text-muted mb-3">has successfully completed</div>
            <h4 style="font-weight:700">{{ $video->title }}</h4>

            <div class="mt-4 mb-3">
                <div class="d-flex justify-content-center gap-5">
                    <div class="text-center">
                        <div class="small text-muted">Date</div>
                        <div style="font-weight:700">{{ $tc->completed_at->format('F j, Y') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="small text-muted">Certificate ID</div>
                        <div style="font-weight:700">#{{ str_pad($tc->id ?? $tc->video_id,6,'0',STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            <div style="margin-top:28px;display:flex;align-items:center;justify-content:center;gap:80px">
                <div style="text-align:center">
                    <div style="height:64px;width:220px;border-top:2px solid #ddd"></div>
                    <div class="small text-muted">Trainer / Issuer</div>
                </div>
                <div style="text-align:center">
                    <div style="height:64px;width:220px;border-top:2px solid #ddd"></div>
                    <div class="small text-muted">Authorized Signature</div>
                </div>
            </div>

            <div class="mt-4">
                <a href="#" onclick="window.print();return false;" class="btn btn-primary no-print">Print / Save PDF</a>
            </div>
        </div>
    </div>
</div>
        @endsection
