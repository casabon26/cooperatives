<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Certificate</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background:white; color:#111; }
        .cert-wrap { width:100%; max-width:900px; margin:24px auto; padding:28px; border-radius:10px; background:linear-gradient(135deg,#fff7f8,#fff1f2); }
        .cert-body { background:white; padding:22px; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.06); text-align:center }
        .cert-title { font-weight:800; color:#b91c1c; letter-spacing:1px; margin-bottom:8px }
        h2.name { margin:8px 0; font-size:28px }
        h4.course { margin:6px 0; font-size:18px; font-weight:700 }
        .meta { margin-top:18px; display:flex; justify-content:center; gap:60px }
        .meta .block { text-align:center }
        .small-muted { color:#6b7280; font-size:13px }
        @media print { body { margin:0 } .cert-wrap { box-shadow:none; border:none; } }
    </style>
</head>
<body>
    <div class="cert-wrap">
        <div class="cert-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <div style="text-align:left">
                    <div class="cert-title">CERTIFICATE OF COMPLETION</div>
                    <div class="small-muted">Awarded to</div>
                </div>
                <div style="text-align:right">
                    @if(file_exists(public_path('assets/images/logo/CCLDO.png')))
                        <img src="{{ asset('assets/images/logo/CCLDO.png') }}" alt="logo" style="height:46px;object-fit:contain">
                    @endif
                </div>
            </div>

            <h2 class="name">{{ $user->first_name ? ($user->first_name . ' ' . ($user->last_name ?? '')) : $user->name }}</h2>
            <div class="small-muted">has successfully completed</div>
            <h4 class="course">{{ $video->title }}</h4>

            <div class="meta">
                <div class="block">
                    <div class="small-muted">Date</div>
                    <div style="font-weight:700">{{ $tc->completed_at->format('F j, Y') }}</div>
                </div>
                <div class="block">
                    <div class="small-muted">Certificate ID</div>
                    <div style="font-weight:700">#{{ str_pad($tc->id ?? $tc->video_id,6,'0',STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div style="margin-top:22px;display:flex;justify-content:center;gap:80px">
                <div style="text-align:center">
                    <div style="height:48px;width:220px;border-top:2px solid #ddd"></div>
                    <div class="small-muted">Trainer / Issuer</div>
                </div>
                <div style="text-align:center">
                    <div style="height:48px;width:220px;border-top:2px solid #ddd"></div>
                    <div class="small-muted">Authorized Signature</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print if opened for printing
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.onload = function(){ window.print(); };
        }
    </script>
</body>
</html>
