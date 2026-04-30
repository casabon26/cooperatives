@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        @if(!empty($group) && strtolower($group) === 'cabstop')
            @section('back-button')
                @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Back'])
            @endsection
        @endif
        @if(!empty($group) && strtolower($group) === 'cabstop')
            <h4>Add CabStop (Place) — {{ $group }}</h4>
            <div class="alert alert-info">This form adds a <strong>place choice</strong> that appears in the Livelihood CabStop dropdown. It does <strong>not</strong> create a store location.</div>
        @else
            <h4>Add List Item @if(!empty($group)) — {{ $group }} @endif</h4>
        @endif
        <form method="post" action="{{ route('admin.select_lists.store') }}">
            @csrf
            @include('admin.select_lists._form')
            @if(!empty($group) && strtolower($group) === 'cabstop')
                <button class="btn btn-primary">Create CabStop</button>
            @else
                <button class="btn btn-primary">Create</button>
            @endif
            <a href="{{ route('admin.select_lists.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
