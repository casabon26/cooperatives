@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Resource</h5>
            <form method="post" action="{{ route('admin.cooperative-resources.update', $resource) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.cooperative_resources._form')
            </form>
        </div>
    </div>
</div>
@endsection
