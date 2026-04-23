@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container" style="max-width: 600px;">
        <h4 class="mb-3">Edit Accomplishment Report</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.accomplishment-reports.update', $accomplishmentReport) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.accomplishment_reports._form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        @include('partials.back-button', ['url' => route('admin.accomplishment-reports.index'), 'label' => 'Cancel', 'class' => 'btn-outline-secondary'])
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
