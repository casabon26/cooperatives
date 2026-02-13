@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container" style="max-width: 600px;">
        <h4 class="mb-3">Create Accomplishment Report</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.accomplishment-reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.accomplishment_reports._form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('admin.accomplishment-reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
