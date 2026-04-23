@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-3 text-end">
        @include('partials.back-button', ['url' => '/livelihood', 'label' => 'Back'])
    </div>

    @php
        // Banner / avatar resolution (similar logic to cooperative profile)
        $banner = null;
        if(!empty($slpa->banner)) $banner = $slpa->banner;

        $imgUrl = null;
        if (!empty($slpa->image)) {
            $storagePath = public_path('storage/'.$slpa->image);
            $directPath = public_path($slpa->image);
            $publicCopy = public_path('slpa_images/'.basename($slpa->image));
            if (file_exists($storagePath)) {
                $imgUrl = asset('storage/'.$slpa->image);
            } elseif (file_exists($directPath)) {
                $imgUrl = asset($slpa->image);
            } elseif (file_exists($publicCopy)) {
                $imgUrl = asset('slpa_images/'.basename($slpa->image));
            }
        }

        // Products are provided by controller as paginated `paginator` variable

        $members = $slpa->members_count ?? null;
        $address = $slpa->address ?? null;
        $business = $slpa->business ?? null;
        $mapEmbed = $slpa->map_embed ?? null;
        $gallery = $slpa->gallery ?? [];
        if(!$gallery) $gallery = [];
        $phone = $slpa->contact_phone ?? null;
        $email = $slpa->contact_email ?? null;
        $hours = $slpa->operating_hours ?? null;
        //$ products_description removed — products are stored as simple names
    @endphp

    <!-- Header Section -->
    <header class="mb-3">
        <div class="d-flex align-items-center gap-3">
            @if($imgUrl)
                <div style="flex:0 0 auto;">
                    <img src="{{ $imgUrl }}" alt="{{ $slpa->name }}" style="width:96px;height:96px;object-fit:cover;border-radius:50%;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                </div>
            @endif
            <div class="flex-grow-1">
                <h1 class="h4 mb-0">{{ $slpa->name }}</h1>
                <p class="text-muted mb-0">{{ $business ? $business : 'SLPA' }} {{ ($business && $members) ? '· ' : '' }}{{ $members ? $members.' members' : '' }}</p>
            </div>
        </div>
        @php
            $bannerUrl = null;
            if($banner){
                $storagePath = public_path('storage/'.$banner);
                $directPath = public_path($banner);
                $publicCopy = public_path('slpa_images/'.basename($banner));
                $publicGallery = public_path('slpa_galleries/'.basename($banner));
                if (file_exists($storagePath)) {
                    $bannerUrl = asset('storage/'.$banner);
                } elseif (file_exists($directPath)) {
                    $bannerUrl = asset($banner);
                } elseif (file_exists($publicCopy)) {
                    $bannerUrl = asset('slpa_images/'.basename($banner));
                } elseif (file_exists($publicGallery)) {
                    $bannerUrl = asset('slpa_galleries/'.basename($banner));
                } else {
                    if (basename($banner) === $banner) {
                        $bannerUrl = asset('slpa_galleries/'.basename($banner));
                    } else {
                        $bannerUrl = asset($banner);
                    }
                }
            }
        @endphp

        @if(!empty($bannerUrl))
            <div class="mb-3 mt-3" style="height:180px;background-image:url('{{ $bannerUrl }}');background-size:cover;background-position:center;border-radius:8px;overflow:hidden"></div>
        @endif
    </header>

    <div class="row">
        <div class="col-lg-8">
            <!-- About / Overview -->
            <section class="mb-3">
                <h2 class="h6 fw-bold">About / Overview</h2>
                <div class="mb-2">{!! nl2br(e($slpa->description ?? '')) !!}</div>
                {{-- products_description removed from SLPA; no description block --}}
            </section>

            <section class="mb-3">
                <h2 class="h6 fw-bold">Products</h2>
                @if(isset($paginator) && $paginator->count())
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40%">Product</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paginator as $prod)
                                    <tr>
                                        <td>
                                            @if(is_array($prod) || is_object($prod))
                                                {{ data_get($prod,'name') ?? (is_string(reset($prod)) ? reset($prod) : '') }}
                                            @else
                                                {{ (string)$prod }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $desc = '';
                                                if (is_array($prod) || is_object($prod)) {
                                                    $desc = data_get($prod,'description','');
                                                }
                                            @endphp
                                            {!! nl2br(e($desc)) !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($paginator->lastPage() > 1)
                        <nav aria-label="Products pagination" class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">&laquo; Prev</a>
                                </li>
                                @for($i = 1; $i <= $paginator->lastPage(); $i++)
                                    <li class="page-item {{ $paginator->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">Next &raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    @endif
                @else
                    <div class="small text-muted">No products listed.</div>
                @endif
            </section>

            <section class="mb-3">
                <h2 class="h6 fw-bold">Location & Reach</h2>
                <div class="mb-2">{{ $address ?? 'Address not provided' }}</div>
                @if($mapEmbed)
                    <div style="height:240px;">{!! $mapEmbed !!}</div>
                @endif
            </section>

            <section class="mb-3">
                <h2 class="h6 fw-bold">Gallery</h2>
                @if(is_array($gallery) && count($gallery))
                    <div class="row g-2">
                        @foreach($gallery as $img)
                            @php
                                $gUrl = null;
                                if (strpos($img,'http') === 0) {
                                    $gUrl = $img;
                                } else {
                                    $storagePath = public_path('storage/'.$img);
                                    $directPath = public_path($img);
                                    $publicGallery = public_path('slpa_galleries/'.basename($img));
                                    if (file_exists($storagePath)) {
                                        $gUrl = asset('storage/'.$img);
                                    } elseif (file_exists($directPath)) {
                                        $gUrl = asset($img);
                                    } elseif (file_exists($publicGallery)) {
                                        $gUrl = asset('slpa_galleries/'.basename($img));
                                    } else {
                                        if (basename($img) === $img) {
                                            $gUrl = asset('slpa_galleries/'.basename($img));
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

            <section class="mb-3">
                <h2 class="h6 fw-bold">Contact</h2>
                <ul class="list-unstyled small">
                    @if($phone)<li><strong>Phone:</strong> {{ $phone }}</li>@endif
                    @if($email)<li><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                    @if($hours)<li><strong>Operating hours:</strong> {{ $hours }}</li>@endif
                </ul>
            </section>

        </div>

        <aside class="col-lg-4">
            <div class="mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12"><strong>Members</strong><div class="small text-muted">{{ $members ?? '—' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>

        </aside>
    </div>
</div>
@endsection
