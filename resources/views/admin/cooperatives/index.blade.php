@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('coopSearch');
    var container = document.getElementById('cooperativesContainer');
    if(!input || !container) return;
    var timer = null;

    function doSearch(q){
        var url = new URL("{{ route('admin.cooperatives.search') }}", window.location.origin);
        if(q) url.searchParams.set('q', q);
        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r){ if(!r.ok) throw r; return r.text(); })
        .then(function(html){ container.innerHTML = html; })
        .catch(function(err){ console.warn('Search failed', err); });
    }

    input.addEventListener('input', function(){
        clearTimeout(timer);
        timer = setTimeout(function(){ doSearch(input.value.trim()); }, 220);
    });
});
</script>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Cooperatives</h4>
        <div>
            <a href="{{ route('admin.cooperatives.view') }}" class="btn btn-sm btn-primary me-2" target="_self">View Cooperatives</a>
            <a href="{{ route('admin.cooperatives.create') }}" class="btn btn-sm btn-primary me-2" target="_self">Create Cooperative</a>
        </div>
    </div>

    <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <input id="coopSearch" type="search" class="form-control form-control-sm" placeholder="Search cooperatives (type to filter)..." aria-label="Search cooperatives">
                </div>

                <div id="cooperativesContainer">
                    @include('admin.cooperatives._list', ['cooperatives' => $cooperatives])
                </div>
            </div>
    </div>
</div>
@endsection
