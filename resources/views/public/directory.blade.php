@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">Cooperatives Directory</h1>
    <form class="row g-2 my-3" method="get" role="search" aria-label="Search cooperatives">
        <div class="col-md-6"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name"></div>
        <div class="col-md-3"><input name="region" value="{{ request('region') }}" class="form-control" placeholder="Region"></div>
        <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
    </form>

    <div class="row row-cols-1 row-cols-md-3 g-3">
        @foreach($cooperatives as $coop)
            <div class="col">
                <article class="card h-100">
                    <div class="card-body">
                        <h3 class="h6"><a href="{{ route('cooperatives.profile',$coop) }}">{{ $coop->name }}</a></h3>
                        <p class="small text-muted">{{ $coop->sector }} · {{ $coop->region }}</p>
                        <p class="mb-0">{{ Str::limit($coop->description,120) }}</p>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    <div class="mt-3">{{ $cooperatives->links() }}</div>
</div>
@endsection
