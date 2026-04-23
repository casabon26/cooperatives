@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => route('admin.enterprises.index'), 'label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Import Enterprises (CSV / Excel)</h4>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(empty($hasSpreadsheet))
                <div class="alert alert-warning small">XLS/XLSX parsing is not enabled on this server. Please upload a <strong>CSV</strong> file or install PhpSpreadsheet: <code>composer require phpoffice/phpspreadsheet</code></div>
            @endif

            <form id="enterprise-import-form" method="POST" action="{{ route('admin.enterprises.import.process') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">CSV / Excel file</label>
                    <input type="file" name="file" id="import-file" class="form-control" accept=".csv,.xls,.xlsx" required>
                </div>
                <button type="submit" class="btn btn-accent">Import</button>
            </form>

            <div id="import-loading" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.85);z-index:1060;align-items:center;justify-content:center">
                <div style="text-align:center">
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem"></div>
                    <div class="mt-2">Importing... please wait</div>
                </div>
            </div>

@section('scripts')
            <script>
        (function(){
            const form = document.getElementById('enterprise-import-form');
            const fileInput = document.getElementById('import-file');
            const loader = document.getElementById('import-loading');
            const HAS_SPREADSHEET = @json($hasSpreadsheet ?? false);
            if(!form) return;
            form.addEventListener('submit', function(e){
                if(!fileInput || !fileInput.files || !fileInput.files.length){
                    e.preventDefault();
                    alert('Please choose a CSV or Excel file to import.');
                    return;
                }
                // Client-side check: if user selected XLS/XLSX but server lacks PhpSpreadsheet, block early
                try{
                    const name = (fileInput.files[0].name || '').toLowerCase();
                    const ext = name.split('.').pop();
                    if((ext === 'xls' || ext === 'xlsx') && !HAS_SPREADSHEET){
                        e.preventDefault();
                        alert('XLS/XLSX parsing is not enabled on the server. Please upload a CSV file or run: composer require phpoffice/phpspreadsheet');
                        if(loader) loader.style.display = 'none';
                        return;
                    }
                }catch(err){ /* ignore */ }

                if(loader) loader.style.display = 'flex';
            });
        })();
    </script>
@endsection

            <hr>
            <p class="small text-muted">Notes:
                <ul>
                    <li>Rows with missing BUSINESS NAME will be skipped.</li>
                    <li>If SIZE is not one of the allowed values it will default to <code>Micro</code>.</li>
                    <li>If no image exists for the enterprise, a default placeholder will be assigned.</li>
                </ul>
            </p>
        </div>
    </div>
</div>
@endsection
