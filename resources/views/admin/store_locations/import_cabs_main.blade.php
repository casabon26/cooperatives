@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3>Import CABS MAIN Stores (Excel)</h3>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <p class="small text-muted">Upload your Excel file (.xlsx or .xls). The first row should be headers like "NAME OF OWNER", "BUSINESS NAME", "STATUS", "STORE TYPE". Place will be set to <strong>CABS MAIN</strong>. Existing records (matched by name + place) will be updated unless you check "Do not update existing".</p>
      <form method="POST" action="{{ route('admin.store_locations.cabs_import') }}" enctype="multipart/form-data" target="_self">
        @csrf
        <div class="mb-3">
          <label class="form-label">Excel file (.xlsx / .xls)</label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" class="form-control" required>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="no_update" id="no_update" value="1">
          <label class="form-check-label" for="no_update">Do not update existing</label>
        </div>
        <div>
          <button class="btn btn-primary">Upload and Import</button>
          @include('partials.back-button', ['url' => route('admin.store_locations.index'), 'label' => 'Cancel', 'class' => 'btn-link ms-2'])
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
