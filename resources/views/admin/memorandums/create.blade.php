@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <h4>Create Memorandum</h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.memorandums.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @include('admin.memorandums._form')
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('admin.memorandums.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
