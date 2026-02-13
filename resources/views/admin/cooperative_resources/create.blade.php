@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add Cooperative Resource</h5>
            <form method="post" action="{{ route('admin.cooperative-resources.store') }}" enctype="multipart/form-data">
                @include('admin.cooperative_resources._form')
            </form>
        </div>
    </div>
</div>
@endsection
