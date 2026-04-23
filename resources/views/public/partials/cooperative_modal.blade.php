<div class="coop-modal text-center">
    @php
        $imgUrl = null;
        if (!empty($cooperative->image)) {
            $storagePath = public_path('storage/'.$cooperative->image);
            $directPath = public_path($cooperative->image);
            $publicCopy = public_path('cooperative_images/'.basename($cooperative->image));
            if (file_exists($storagePath)) {
                $imgUrl = asset('storage/'.$cooperative->image);
            } elseif (file_exists($directPath)) {
                $imgUrl = asset($cooperative->image);
            } elseif (file_exists($publicCopy)) {
                $imgUrl = asset('cooperative_images/'.basename($cooperative->image));
            }
        }
        if (!$imgUrl && !empty($cooperative->image ?? null)) {
            // no separate profile image column after consolidation; fallback to cooperative image
            $p = $cooperative->image;
            $storagePath = public_path('storage/'.$p);
            $directPath = public_path($p);
            $publicCopy = public_path('cooperative_images/'.basename($p));
            if (file_exists($storagePath)) {
                $imgUrl = asset('storage/'.$p);
            } elseif (file_exists($directPath)) {
                $imgUrl = asset($p);
            } elseif (file_exists($publicCopy)) {
                $imgUrl = asset('cooperative_images/'.basename($p));
            }
        }
    @endphp

    <div class="mb-3">
        @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="{{ $cooperative->name }}" class="rounded-circle" style="width:110px;height:110px;object-fit:cover;display:block;margin:0 auto;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
        @else
            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:110px;height:110px;margin:0 auto;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path></svg>
            </div>
        @endif
    </div>

    <h3 class="h6 mb-0 fw-bold">{{ $cooperative->name }}</h3>
    <p class="small text-muted mb-2">Cooperative · {{ $cooperative->sector ?? '' }}{{ ($cooperative->sector && $cooperative->region) ? ' · ' : '' }}{{ $cooperative->region ?? '' }}</p>

    @if($cooperative->description)
        <p class="mb-2 text-center">{{ $cooperative->description }}</p>
    @endif

{{-- Mission & Vision Section --}}
@php
    // Special-case fallback for CAMAVEMCO (keep your existing one)
    $nameKey = strtolower(trim($cooperative->name ?? ''));

    $isCamavemco = str_contains($nameKey, 'camavemco');

    // New cooperatives data
    $coopData = [
        'kababaihan kaibigan ng bigaa' => [
            'mission' => "To empower women and the community of Barangay Bigaa through mutual aid, livelihood support, and social responsibility.",
            'vision'  => "Providing essential supplies and safety equipment to members and the local community."
        ],
        'cabueños transport cooperative' => [
            'mission' => "CTC is a transport cooperative formed by operators and drivers to have access to the government’s Public Utility Vehicle Modernization Program (PUVMP) aimed at providing its members with stable, sufficient and dignified livelihood while at the same time ensuring the public safe, comfortable and environmentally-friendly transport service.",
            'vision'  => "Cabueños Transport Cooperative is a name synonymous to safe, reliable and trustworthy transport system in Cabuyao and its neighboring towns; where members are assured of stable, sufficient and dignified livelihood."
        ],
        'cabuyao agriculture and fishery' => [
            'mission' => "Mapataas ang antas ng Kabuhayan ng bawat pamilya ng kasapi.\nMakapagtatag ng isang tindahan ng mga produktong pang agrikultura at pampangisdaan.\nMapagbuklod ang lahat ng mga magsasaka at mangingisda sa Lungsod ng Cabuyao.\nMatugunan ang pangangailangan sa bigas, gulay at isda ng mga mamamayan.\nMapalakas ang negosyo pamamagitan ng aktibong pakikipagugnayan sa iba pang kooperatiba.",
            'vision'  => "Isang kooperatiba matatag, maunlad at matagumpay na nagbubuklod sa mga magsasaka, mangingisda at kaugnay na sector sa Lungsod ng Cabuyao."
        ],
        'mrvsacc' => [
            'mission' => "MRVSACC is committed to providing financial assistance through loan facilities to its members most especially those who are engaged in the business sector.",
            'vision'  => "MRVSACC aims to be one of the leading cooperatives in Cabuyao City by 2026."
        ],
        'nexperia employees cooperative' => [
            'mission' => "To sustain growth within the area of operations, expand growth within the province of Laguna, provide the best services for members as well as community development programs – a continuous partner of Nexperia Cabuyao to achieve common benefits to each employee and members.",
            'vision'  => "To be the leading institutional cooperative within the province of Laguna in terms of Return of Capital Share, Membership, Business Exposure, Other Loans and Business Services."
        ],
        'cabuyao ofw consumers cooperative' => [
            'mission' => "To provide returning OFWs with opportunities for investment, business, and livelihood stability through a cooperative structure.",
            'vision'  => "To empower OFW members to have a prosperous and sustainable life in the Philippines through shared resources and economic participation."
        ],
        'ja services cooperative' => [
            'mission' => "To provide accessible and efficient services, promote economic opportunities, and strengthen cooperative values among members through responsible governance and sustainable business operations.",
            'vision'  => "A progressive, sustainable, and competitive cooperative that empowers its members and contributes to community development."
        ],
        'go ladies producers cooperative' => [
            'mission' => "GO Ladies’ Mission is to strengthen the leadership, power, voices of women. To support women to achieve their full potential, to encourage and facilitate their active involvement in business, learning and community life.",
            'vision'  => "GO Ladies’ Vision is that all women in Cabuyao become self-sustaining and remain well recognized in their community, well respected and truly empowered."
        ],
    ];

    // Normalize cooperative name (remove non-alphanum) for more robust matching
    $nameKeyRaw = strtolower(trim($cooperative->name ?? ''));
    $nameKey = preg_replace('/[^a-z0-9]/', '', $nameKeyRaw);

    $missionText = $cooperative->mission ?? null;
    $visionText  = $cooperative->vision  ?? null;

    foreach ($coopData as $key => $data) {
        $kNorm = preg_replace('/[^a-z0-9]/', '', strtolower($key));
        if ($kNorm !== '' && $nameKey !== '' && strpos($nameKey, $kNorm) !== false) {
            $missionText = $missionText ?? $data['mission'];
            $visionText  = $visionText  ?? $data['vision'];
            break;
        }
    }

    // CAMAVEMCO fallback (kept as before)
    if ($isCamavemco) {
        $missionText = $missionText ?? "Maisagawa ang magandang pananaw ng kooperatiba sa pamamagitan ng mga opisyal, pagpapahalaga at pagmamahal sa layunin ng kooperatiba.\n\nMaimulat ang mga kasapin at mamamayan sa magandang layunin ng kooperatiba na mapaunlad ang antas ng kabuhayan ng bawat isa sa pamamagitan ng sipag, tiyaga at pagiimpok at serbisyong maibibigay ng kooperatiba.";
        $visionText  = $visionText  ?? "Nangunguna at pinagkakatiwalaang community-base cooperative sa lunsod ng Cabuyao.";
    }
@endphp

{{-- Display Mission & Vision Cards --}}
@if($missionText || $visionText)
    <div class="d-flex justify-content-center gap-2 mb-3 flex-column flex-md-row">
        @if($missionText)
            <div class="card flex-fill" style="max-width:280px;">
                <div class="card-body p-3 text-center">
                    <h6 class="card-title mb-2 fw-bold text-primary">Mission</h6>
                    <p class="card-text small mb-0">{!! nl2br(e($missionText)) !!}</p>
                </div>
            </div>
        @endif

        @if($visionText)
            <div class="card flex-fill" style="max-width:280px;">
                <div class="card-body p-3 text-center">
                    <h6 class="card-title mb-2 fw-bold text-success">Vision</h6>
                    <p class="card-text small mb-0">{!! nl2br(e($visionText)) !!}</p>
                </div>
            </div>
        @endif
    </div>
@endif

    {{-- Social links row (shows only if any link exists) --}}
    @php
        $socials = [];
        // prefer explicit social fields if available on cooperative, otherwise infer from generic link
        if(!empty($cooperative->facebook ?? null)) $socials['facebook'] = $cooperative->facebook;
        if(!empty($cooperative->twitter ?? null)) $socials['twitter'] = $cooperative->twitter;
        if(!empty($cooperative->instagram ?? null)) $socials['instagram'] = $cooperative->instagram;
        if(!empty($cooperative->linkedin ?? null)) $socials['linkedin'] = $cooperative->linkedin;
        if(!empty($cooperative->link ?? null)) {
            $link = $cooperative->link;
            $lc = strtolower($link);
            if((strpos($lc,'facebook.com')!==false) || (strpos($lc,'fb.me')!==false)) {
                if(empty($socials['facebook'])) $socials['facebook'] = $link;
            } elseif(strpos($lc,'twitter.com')!==false) {
                if(empty($socials['twitter'])) $socials['twitter'] = $link;
            } elseif(strpos($lc,'instagram.com')!==false) {
                if(empty($socials['instagram'])) $socials['instagram'] = $link;
            } elseif(strpos($lc,'linkedin.com')!==false) {
                if(empty($socials['linkedin'])) $socials['linkedin'] = $link;
            } else {
                if(empty($socials['website'])) $socials['website'] = $link;
            }
        }
    @endphp

    @if(count($socials))
        <div class="d-flex justify-content-center gap-2 mb-3">
            @foreach($socials as $k => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="d-inline-flex align-items-center justify-content-center border rounded-circle" style="width:36px;height:36px;background:#fff;text-decoration:none;color:#495057;">
                    @if($k==='facebook')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2.2v-2.9h2.2V9.3c0-2.2 1.3-3.4 3.2-3.4.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.3v1.6h2.3l-.4 2.9h-1.9v7A10 10 0 0 0 22 12z"/></svg>
                    @elseif($k==='twitter')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53A4.48 4.48 0 0 0 12 7v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    @elseif($k==='instagram')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 6.3A4.7 4.7 0 1 0 16.7 13 4.7 4.7 0 0 0 12 8.3zm6.4-3.1a1.2 1.2 0 1 1-1.2 1.2 1.2 1.2 0 0 1 1.2-1.2z"/></svg>
                    @elseif($k==='linkedin')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-14h4v2a4 4 0 0 1 4-2zM2 9h4v14H2zM4 3a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>
                    @elseif($k==='website')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 17.9V19a8 8 0 0 0 3.9-3.1A8.3 8.3 0 0 1 13 19.9zM6.1 16.1A8 8 0 0 0 11 19v.9A8.3 8.3 0 0 1 6.1 16.1zM4.1 13H7a13 13 0 0 1 .6-3.7A8.1 8.1 0 0 0 4.1 13zM12 4.1V7a8 8 0 0 1 3.1.9A8.3 8.3 0 0 0 12 4.1z"/></svg>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    @if($cooperative->documents && $cooperative->documents->count())
        <hr>
        <h6 class="mb-2 text-start">Documents</h6>
        <ul class="list-unstyled small mb-0 text-start">
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