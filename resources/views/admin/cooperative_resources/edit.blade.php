@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Resource</h5>
            @if(isset($cooperative))
                <form method="post" action="{{ route('admin.cooperatives.resources.update', [$cooperative, $resource]) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('admin.cooperative_resources._form')
                </form>
            @else
                <form method="post" action="{{ route('admin.cooperative-resources.update', $resource) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('admin.cooperative_resources._form')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
