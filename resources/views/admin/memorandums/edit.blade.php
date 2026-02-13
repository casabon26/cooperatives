@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <h4>Edit Memorandum</h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.memorandums.update', $memorandum) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.memorandums._form')
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.memorandums.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" role="button" aria-label="Cancel and return to list" title="Cancel" target="_self">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Cancel
            </a>
        </form>
    </div>
</div>
@endsection
