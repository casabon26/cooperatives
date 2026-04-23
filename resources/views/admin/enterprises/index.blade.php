@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['label' => 'Back', 'class' => 'btn-sm'])
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Manage Enterprises</h4>
        <div>
            <a href="{{ route('admin.enterprises.create') }}" class="btn btn-sm btn-primary me-2">Create Enterprise</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form method="GET" action="{{ route('admin.enterprises.index') }}" class="mb-3">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Search Enterprises</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, industry, or address..." value="{{ $search ?? '' }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if($search || $size)
                            <a href="{{ route('admin.enterprises.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <div>
                <label class="form-label small fw-semibold text-muted d-block mb-2">Filter by Size</label>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($sizes as $sizeOption)
                        <a href="{{ route('admin.enterprises.index', array_merge(request()->query(), ['size' => $sizeOption])) }}" 
                           class="btn btn-sm transition-all {{ $size === $sizeOption ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-building me-1"></i>{{ $sizeOption }}
                        </a>
                    @endforeach
                    @if($size)
                        <a href="{{ route('admin.enterprises.index', ['search' => $search]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear Filter
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(isset($enterprises) && $enterprises->count())
                <form method="POST" action="{{ route('admin.enterprises.bulk_delete') }}" id="bulk-delete-form" data-confirm="Delete selected enterprises?">
                    @csrf
                    <input type="hidden" name="select_all" id="select-all-hidden" value="0">
                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                    <input type="hidden" name="size" value="{{ $size ?? '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="select-all">
                            <label class="form-check-label" for="select-all">Select All</label>
                        </div>
                        <button type="submit" id="bulk-delete-btn" class="btn btn-sm btn-danger" disabled>Delete Selected</button>
                    </div>
                    <div id="select-all-message" class="small text-muted mb-2" style="display:none"></div>

                    <div class="list-group">
                        @foreach($enterprises as $e)
                        <div class="list-group-item enterprise-row d-flex align-items-center">
                            <div class="me-3">
                                <input type="checkbox" name="ids[]" value="{{ $e->id }}" class="form-check-input select-enterprise">
                            </div>
                            <div class="content flex-grow-1 d-flex align-items-center">
                                @if(!empty($e->image_url))
                                    <img src="{{ $e->image_url }}" alt="" style="width:84px;height:56px;object-fit:cover;border-radius:6px;">
                                @endif
                                <div class="text ms-3">
                                    <div class="fw-semibold">{{ $e->name }}</div>
                                    <div class="small text-muted">{{ $e->category }} · {{ $e->industry ?? '—' }} · {{ $e->address ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="text-end d-flex flex-column justify-content-center gap-1 actions">
                                <a href="{{ route('admin.enterprises.edit', $e->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <button type="button" class="btn btn-sm btn-outline-danger single-delete-btn" data-id="{{ $e->id }}" style="margin-top:.25rem">Delete</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
                <div class="mt-3">{{ $enterprises->links() }}</div>
            @else
                <div class="alert alert-info">No enterprises created yet.</div>
            @endif
        </div>
    </div>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectAll = document.getElementById('select-all');
    const bulkBtn = document.getElementById('bulk-delete-btn');

    function updateBulkBtn(){
        const anyChecked = document.querySelectorAll('.select-enterprise:checked').length > 0;
        const selectAllFlag = document.getElementById('select-all-hidden') && document.getElementById('select-all-hidden').value === '1';
        if(bulkBtn) bulkBtn.disabled = !(anyChecked || selectAllFlag);
    }

    const selectAllMessage = document.getElementById('select-all-message');
    if(selectAll){
        selectAll.addEventListener('change', function(){
            document.querySelectorAll('.select-enterprise').forEach(c => c.checked = this.checked);
            // reset select_all hidden flag when toggling select-all
            const hidden = document.getElementById('select-all-hidden');
            if(hidden) hidden.value = '0';
            // show option to select all results across pages
            if(this.checked && selectAllMessage){
                const pageCount = {{ $enterprises->count() }};
                const total = {{ $enterprises->total() }};
                if(total > pageCount){
                    selectAllMessage.style.display = 'block';
                    selectAllMessage.innerHTML = `All ${pageCount} on this page selected. <a href="#" id="select-all-results">Select all ${total} results</a>`;
                    const link = document.getElementById('select-all-results');
                    if(link) link.addEventListener('click', function(ev){ ev.preventDefault(); const hidden = document.getElementById('select-all-hidden'); if(hidden) hidden.value = '1'; selectAllMessage.innerHTML = 'All ${total} results selected.'; updateBulkBtn(); });
                } else if(selectAllMessage){
                    selectAllMessage.style.display = 'none';
                }
            } else if(selectAllMessage){
                selectAllMessage.style.display = 'none';
            }
            updateBulkBtn();
        });
    }

    document.querySelectorAll('.select-enterprise').forEach(cb => cb.addEventListener('change', function(){
        // clear the select_all flag if any individual change occurs
        const hidden = document.getElementById('select-all-hidden');
        if(hidden) hidden.value = '0';
        if(!this.checked && selectAll) selectAll.checked = false;
        else if(selectAll){
            const all = Array.from(document.querySelectorAll('.select-enterprise'));
            selectAll.checked = all.every(c => c.checked);
        }
        if(selectAllMessage) selectAllMessage.style.display = 'none';
        updateBulkBtn();
    }));

    // Single-delete buttons: create and submit a temporary form to avoid nested forms
    document.querySelectorAll('.single-delete-btn').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id;
        window.appConfirm('Delete this enterprise?').then(function(ok){
            if(!ok) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/enterprises/' + id;
            form.style.display = 'none';
            const token = '{{ csrf_token() }}';
            form.innerHTML = '<input type="hidden" name="_token" value="'+token+'">' +
                             '<input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        });
    }));
});
</script>
@endsection

@endsection
