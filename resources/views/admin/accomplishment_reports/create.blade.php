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
                        @include('partials.back-button', ['url' => route('admin.accomplishment-reports.index'), 'label' => 'Cancel', 'class' => 'btn-outline-secondary'])
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.querySelector('form[action="{{ route('admin.accomplishment-reports.store') }}"]');
    if(!form) return;
    form.addEventListener('submit', function(e){
        try{
            var fileInput = form.querySelector('input[type="file"][name="file"]');
            if(!fileInput || !fileInput.files || fileInput.files.length === 0) return; // only show when a file is being uploaded

            // create overlay
            var overlay = document.createElement('div');
            overlay.id = 'uploadOverlay';
            overlay.style.position = 'fixed';
            overlay.style.inset = '0';
            overlay.style.background = 'rgba(0,0,0,0.36)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '2050';
            overlay.innerHTML = '<div class="text-center text-white"><div class="spinner-border text-light" role="status" style="width:3rem;height:3rem"></div><div class="mt-2">Uploading... Please wait</div></div>';
            document.body.appendChild(overlay);

            // disable submit button
            var submitBtn = form.querySelector('button[type="submit"]');
            if(submitBtn){
                submitBtn.disabled = true;
                submitBtn.dataset._orig = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Uploading...';
            }
        }catch(err){ /* ignore JS errors and allow submit */ }
    });
});
</script>
@endsection
