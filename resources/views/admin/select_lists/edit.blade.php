@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        @if(!empty($group) && strtolower($group) === 'cabstop')
            @section('back-button')
                @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Back'])
            @endsection
        @endif
        <h4>Edit List Item @if(!empty($group)) — {{ $group }} @endif</h4>
        <form method="post" action="{{ route('admin.select_lists.update', $item) }}">
            @csrf
            @method('PUT')
            @include('admin.select_lists._form')
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.select_lists.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
