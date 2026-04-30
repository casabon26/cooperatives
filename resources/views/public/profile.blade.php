@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-3 text-end">
        @include('partials.back-button', ['url' => '/cooperatives?per_page=34', 'label' => 'Back'])
    </div>

    @php
        // Banner image resolution: prefer cooperative.image
        $banner = null;
        if(!empty($cooperative->image)) $banner = $cooperative->image;
        // Avatar image resolution similar to modal (tries multiple locations)
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
        // no profile-specific fallback (data now lives on cooperatives)
        // Services list parsing
        $servicesRaw = data_get($cooperative, 'services');
        $services = $servicesRaw ? preg_split('/\r?\n|\|/', $servicesRaw) : [];
        $purpose = $cooperative->objectives ?? null;
        $operations = $cooperative->operations ?? null;
        $years = $cooperative->years ?? null;
        $achievements = $cooperative->achievements ?? null;
        $members = $cooperative->members_count ?? null;
        $address = $cooperative->address ?? null;
        $mapEmbed = $cooperative->map_embed ?? null;
        // gallery may store relative paths or bare filenames
        $gallery = $cooperative->gallery ?? [];
        if(!$gallery) $gallery = [];
        $phone = $cooperative->contact_phone ?? null;
        $email = $cooperative->contact_email ?? null;
        $facebook = $cooperative->facebook ?? null;
        $hours = $cooperative->operating_hours ?? null;
        // Prepare mission/vision fallbacks (used to display beside Purpose)
        $nameKey = strtolower(trim($cooperative->name ?? ''));
        $isCamavemco = str_contains($nameKey, 'camavemco');
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
        $missionText = $cooperative->mission ?? null;
        $visionText  = $cooperative->vision ?? null;
        $nameKeyNorm = preg_replace('/[^a-z0-9]/', '', $nameKey);
        foreach ($coopData as $key => $data) {
            $kNorm = preg_replace('/[^a-z0-9]/', '', strtolower($key));
            if ($kNorm !== '' && $nameKeyNorm !== '' && strpos($nameKeyNorm, $kNorm) !== false) {
                $missionText = $missionText ?? $data['mission'];
                $visionText  = $visionText  ?? $data['vision'];
                break;
            }
        }
        if ($isCamavemco) {
            $missionText = $missionText ?? "Maisagawa ang magandang pananaw ng kooperatiba sa pamamagitan ng mga opisyal, pagpapahalaga at pagmamahal sa layunin ng kooperatiba.\n\nMaimulat ang mga kasapin at mamamayan sa magandang layunin ng kooperatiba na mapaunlad ang antas ng kabuhayan ng bawat isa sa pamamagitan ng sipag, tiyaga at pagiimpok at serbisyong maibibigay ng kooperatiba.";
            $visionText  = $visionText  ?? "Nangunguna at pinagkakatiwalaang community-base cooperative sa lunsod ng Cabuyao.";
        }
        $missionText = $missionText ?? '—';
        $visionText = $visionText ?? '—';
    @endphp

    <!-- Header Section -->
    <header class="mb-3">
        <div class="d-flex align-items-center gap-3">
            @if($imgUrl)
                <div style="flex:0 0 auto;">
                    <img src="{{ $imgUrl }}" alt="{{ $cooperative->name }}" style="width:96px;height:96px;object-fit:cover;border-radius:50%;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                </div>
            @endif
            <div class="flex-grow-1">
                <h1 class="h4 mb-0">{{ $cooperative->name }}</h1>
                <p class="text-muted mb-0">{{ $cooperative->sector }} {{ ($cooperative->sector && $cooperative->region) ? '· ' : '' }}{{ $cooperative->region }}</p>
            </div>
        </div>
        @php
            // resolve banner URL similar to avatar logic
            $bannerUrl = null;
            if($banner){
                $storagePath = public_path('storage/'.$banner);
                $directPath = public_path($banner);
                $publicCopy = public_path('cooperative_images/'.basename($banner));
                $publicGallery = public_path('cooperative_galleries/'.basename($banner));
                if (file_exists($storagePath)) {
                    $bannerUrl = asset('storage/'.$banner);
                } elseif (file_exists($directPath)) {
                    $bannerUrl = asset($banner);
                } elseif (file_exists($publicCopy)) {
                    $bannerUrl = asset('cooperative_images/'.basename($banner));
                } elseif (file_exists($publicGallery)) {
                    $bannerUrl = asset('cooperative_galleries/'.basename($banner));
                } else {
                    // if banner is just a filename, try cooperative_galleries as fallback
                    if (basename($banner) === $banner) {
                        $bannerUrl = asset('cooperative_galleries/'.basename($banner));
                    } else {
                        $bannerUrl = asset($banner);
                    }
                }
            }
        @endphp

        @if(!empty($bannerUrl))
            <div class="mb-3 mt-3" style="height:200px;background-image:url('{{ $bannerUrl }}');background-size:cover;background-position:center;border-radius:8px;overflow:hidden"></div>
        @endif
    </header>

    {{-- Directory card edit removed from public profile (admin editing moved to admin panel) --}}

    <!-- About / Purpose with Mission & Vision to the right -->
    <section class="mb-3">
        <h2 class="h6">About / Overview</h2>
        <div class="row g-3">
            <div class="col-lg-8">
                <style>
                    .coop-overview { line-height:1.65; color:#24303b; }
                    .coop-overview p { margin-bottom:.75rem; }
                    .coop-overview p.lead { font-weight:600; font-size:1.04rem; color:#0f172a; }
                    .coop-purpose { background: linear-gradient(180deg,#fff9f6,#fffaf8); border:1px solid rgba(249,115,22,0.08); padding:1rem; border-radius:8px; }
                    .coop-purpose .title { font-weight:700; color:#b45309; margin-bottom:.5rem; }
                    .coop-purpose p { margin-bottom:.6rem; color:#334155; line-height:1.55; }
                </style>
                @php
                    $descRaw = $cooperative->description ?? ($cooperative->profile->overview ?? '');
                    $desc = trim((string)$descRaw);
                    $paragraphs = [];
                    if ($desc !== '') {
                        // split on empty line(s) to create paragraphs
                        $paragraphs = preg_split('/\r?\n\r?\n+/', $desc);
                    }
                @endphp

                <div class="card mb-2">
                    <div class="card-body coop-overview">
                        @if($desc === '')
                            <div class="small text-muted">No overview provided.</div>
                        @else
                            @foreach($paragraphs as $i => $p)
                                <p class="{{ $i === 0 ? 'lead' : '' }}">{!! nl2br(e(trim($p))) !!}</p>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if($purpose)
                    <h3 class="h6">Purpose</h3>
                    @php
                        $purposeRaw = trim((string)$purpose);
                        if ($purposeRaw === '') {
                            $purposeHtml = '<div class="small text-muted">No purpose provided.</div>';
                        } else {
                            $paras = preg_split('/\r?\n+/', $purposeRaw);
                            $out = '';
                            foreach ($paras as $p) {
                                $out .= '<p>'.nl2br(e(trim($p))).'</p>';
                            }
                            $purposeHtml = $out;
                        }
                    @endphp
                    <div class="coop-purpose mb-3">
                        {!! $purposeHtml !!}
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="mb-3">
                    <div class="card">
                        <div class="card-body d-flex gap-2 align-items-start">
                            <div style="flex:0 0 36px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#fee2e2,#fdd2d2);border-radius:6px;color:#991b1b;font-weight:700;height:40px;">M</div>
                            <div>
                                <h6 class="mb-1">Mission</h6>
                                <div class="small text-muted">{!! nl2br(e($missionText)) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card">
                        <div class="card-body d-flex gap-2 align-items-start">
                            <div style="flex:0 0 36px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef2ff,#e0e7ff);border-radius:6px;color:#1e3a8a;font-weight:700;height:40px;">V</div>
                            <div>
                                <h6 class="mb-1">Vision</h6>
                                <div class="small text-muted">{!! nl2br(e($visionText)) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8">
            <!-- Services / Offerings -->
            <section class="mb-3">
        <h2 class="h6">Services / Offerings</h2>
        @if(count($services))
            <ul class="list-unstyled row g-2">
                @foreach($services as $s)
                    <li class="col-12 col-md-6"><span class="fw-bold">•</span> <span>{{ trim($s) }}</span></li>
                @endforeach
            </ul>
        @else
            <div class="small text-muted">No services listed.</div>
        @endif
        @if($operations)
            <h3 class="h6 mt-2">Operations / How it works</h3>
            <div class="mb-2">{!! nl2br(e($operations)) !!}</div>
        @endif
            </section>

    <!-- Achievements / Key Info -->
            <section class="mb-3">
                <h2 class="h6">Achievements / Key Info</h2>
                <div class="row">
                    <div class="col-6 col-md-4"><strong>Years</strong><div class="small text-muted">{{ $years ?? '—' }}</div></div>
                    <div class="col-12 col-md-8"><strong>Notable accomplishments</strong><div class="small text-muted">{!! nl2br(e($achievements ?? '—')) !!}</div></div>
                </div>
            </section>

    <!-- Members -->
            <section class="mb-3">
        <h2 class="h6">Members</h2>
        @php
            $membersCount = $cooperative->members_count ?? (isset($cooperative->users) ? $cooperative->users->count() : null);
        @endphp
        @if($membersCount !== null)
            <div class="small text-muted">Members: <strong>{{ $membersCount }}</strong></div>
        @else
            <div class="small text-muted">Members: not specified.</div>
        @endif
    </section>

    <!-- Location & Reach -->
            <section class="mb-3">
        <h2 class="h6">Location & Reach</h2>
        <div class="mb-2">{{ $address ?? 'Address not provided' }}</div>
        @if($mapEmbed)
            <div style="height:240px;">{!! $mapEmbed !!}</div>
        @endif
    </section>

    <!-- Gallery -->
            <section class="mb-3">
        <h2 class="h6">Gallery</h2>
        @if(is_array($gallery) && count($gallery))
            <div class="row g-2">
                @foreach($gallery as $img)
                    @php
                        // resolve gallery image path: prefer storage/public path, direct public path, cooperative_galleries public copy,
                        // and as a last resort, treat bare filename as under cooperative_galleries/
                        $gUrl = null;
                        if (strpos($img,'http') === 0) {
                            $gUrl = $img;
                        } else {
                            $storagePath = public_path('storage/'.$img);
                            $directPath = public_path($img);
                            $publicGallery = public_path('cooperative_galleries/'.basename($img));
                            if (file_exists($storagePath)) {
                                $gUrl = asset('storage/'.$img);
                            } elseif (file_exists($directPath)) {
                                $gUrl = asset($img);
                            } elseif (file_exists($publicGallery)) {
                                $gUrl = asset('cooperative_galleries/'.basename($img));
                            } else {
                                // if value is just a filename, assume cooperative_galleries
                                if (basename($img) === $img) {
                                    $gUrl = asset('cooperative_galleries/'.basename($img));
                                } else {
                                    $gUrl = asset($img);
                                }
                            }
                        }
                    @endphp
                    <div class="col-6 col-md-3">
                        <a href="{{ $gUrl }}" target="_blank"><img src="{{ $gUrl }}" class="img-fluid rounded" alt=""></a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="small text-muted">No images available.</div>
        @endif
    </section>

    <!-- Contact Details -->
            <section class="mb-3">
        <h2 class="h6">Contact</h2>
        <ul class="list-unstyled small">
            @if($phone)<li><strong>Phone:</strong> {{ $phone }}</li>@endif
            @if($email)<li><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
            @if($facebook)<li><strong>Facebook:</strong> <a href="{{ $facebook }}" target="_blank">{{ $facebook }}</a></li>@endif
            @if($hours)<li><strong>Operating hours:</strong> {{ $hours }}</li>@endif
        </ul>
    </section>

    <!-- Cooperative Resources (admin-managed files) -->
    @php
        $coopResources = \App\Models\CooperativeResource::where('cooperative_id', $cooperative->id)->orderByDesc('created_at')->get();
        $canManageResources = auth()->check() && (auth()->user()->role === 'gov_admin' || (auth()->user()->role === 'cooperative_admin' && auth()->user()->cooperatives()->where('cooperative_id',$cooperative->id)->exists()));
    @endphp

    <section class="mb-3">
        <h2 class="h6">Files / Resources</h2>

        @if($coopResources->count())
            <div class="list-group">
                @foreach($coopResources as $res)
                    @php
                        $href = $res->file_path ? route('cooperative-resources.file', $res) : ($res->gdrive_link ?? '#');
                        $rawPath = $res->file_path ?? $res->gdrive_link ?? '';
                        $ext = strtolower(pathinfo($rawPath, PATHINFO_EXTENSION));
                        $extLabel = $ext ? strtoupper($ext) : ($res->gdrive_link ? 'URL' : 'FILE');
                        $uploaded = $res->created_at ? $res->created_at->format('M j, Y') : null;
                    @endphp
                    <div class="list-group-item d-flex gap-3 align-items-start">
                        <div style="width:56px;flex:0 0 56px;">
                            <div class="d-flex align-items-center justify-content-center rounded bg-light" style="width:56px;height:56px;border:1px solid rgba(0,0,0,0.04);">
                                <span class="fw-bold text-muted">{{ $extLabel }}</span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ $href }}" target="_blank" class="stretched-link fw-semibold text-decoration-none">{{ $res->title }}</a>
                            @if($res->description)
                                <div class="small text-muted">{{ Str::limit($res->description,140) }}</div>
                            @endif
                            <div class="small text-muted mt-1">
                                @if($uploaded) Uploaded {{ $uploaded }} &middot; @endif
                                <span>{{ $extLabel }}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <a href="{{ $href }}" target="_blank" class="btn btn-sm btn-outline-primary">Open</a>
                            @if($canManageResources)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.cooperatives.resources.edit', [$cooperative, $res]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.cooperatives.resources.destroy', [$cooperative, $res]) }}" class="m-0" data-confirm="Delete resource?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="small text-muted">No files uploaded for this cooperative.</div>
        @endif

        @if($canManageResources)
            <div class="card mt-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cooperatives.resources.store', $cooperative) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" name="title" class="form-control" placeholder="File title" required>
                            </div>
                            <div class="col-md-6">
                                <input type="url" name="gdrive_link" class="form-control" placeholder="Google Drive link (optional)">
                            </div>
                            <div class="col-12 mt-2">
                                <textarea name="description" class="form-control" rows="2" placeholder="Description (optional)"></textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <input type="file" name="file" class="form-control">
                            </div>
                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </section>

    <!-- Documents -->
    @if($cooperative->documents->count())
    <section class="mb-3">
        <h2 class="h6">Documents</h2>
        <ul>
            @foreach($cooperative->documents as $doc)
                <li class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="/storage/{{ $doc->file_path }}">{{ $doc->document_type ?? 'Document' }}</a>
                        <div class="small text-muted">{{ basename($doc->file_path) }}</div>
                    </div>
                    @auth
                        @php
                            $canDelete = in_array(Auth::user()->role ?? '', ['gov_admin','cooperative_admin']) && (Auth::user()->role !== 'cooperative_admin' || Auth::user()->cooperatives()->where('cooperative_id',$cooperative->id)->exists());
                        @endphp
                        @if($canDelete)
                            <form method="POST" action="{{ route('documents.delete', $doc) }}" class="ms-3" data-confirm="Delete this document?">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>
    </section>
    @endif
        </div>

        </aside>
    </div>
</div>
@endsection
