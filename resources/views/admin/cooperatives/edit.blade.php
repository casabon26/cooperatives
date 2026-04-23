@extends('layouts.app')

@section('back-button')
    @include('partials.back-button', ['url' => route('admin.cooperatives.index'), 'label' => 'Back to Cooperatives'])
@endsection

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Edit Cooperative</h1>
    </div>
    <form method="post" action="{{ route('admin.cooperatives.update',$cooperative) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name',$cooperative->name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Sector</label>
            <input name="sector" class="form-control" value="{{ old('sector',$cooperative->sector) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Region</label>
            <input name="region" class="form-control" value="{{ old('region',$cooperative->region) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Link (optional)</label>
            <input name="link" class="form-control" value="{{ old('link', $cooperative->link) }}" placeholder="https://example.com/coop-profile">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending','active','suspended','archived'] as $s)
                    <option value="{{ $s }}" {{ $cooperative->status===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Image (optional)</label>
            @if(!empty($cooperative->image))
                <div class="mb-2">
                    <img src="{{ asset($cooperative->image) }}" alt="{{ $cooperative->name }}" style="max-height:120px;object-fit:cover;border-radius:.35rem;">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-control">
            <div class="form-text">Upload a square-ish image. Will be used as the cooperative card header.</div>
        </div>
        <hr>
        <h4 class="h6 mt-3">Profile / Details</h4>
        @php $p = $cooperative->profile ?? null; @endphp
        <div class="mb-3">
            <label class="form-label">Mission</label>
            <textarea name="mission" class="form-control" rows="3">{{ old('mission', $p->mission ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Vision</label>
            <textarea name="vision" class="form-control" rows="3">{{ old('vision', $p->vision ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">About / Objectives</label>
            <textarea name="objectives" class="form-control" rows="3">{{ old('objectives', $p->objectives ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Services / Offerings</label>
            <textarea name="services" class="form-control" rows="3">{{ old('services', $p->services ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Achievements / Key Info</label>
            <textarea name="achievements" class="form-control" rows="3">{{ old('achievements', $p->achievements ?? '') }}</textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-6 mb-3">
                <label class="form-label">Years</label>
                <input name="years" class="form-control" value="{{ old('years', $p->years ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input name="address" class="form-control" value="{{ old('address', $p->address ?? '') }}">
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-6 mb-3">
                <label class="form-label">Contact Phone</label>
                <input name="contact_phone" class="form-control" value="{{ old('contact_phone', $cooperative->contact_phone ?? $p->contact_phone ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Contact Email</label>
                <input name="contact_email" type="email" class="form-control" value="{{ old('contact_email', $cooperative->contact_email ?? $p->contact_email ?? '') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Gallery</label>
            @if(!empty($p->gallery) && is_array($p->gallery) && count($p->gallery))
                <div class="mb-2 d-flex gap-2 flex-wrap">
                    @foreach($p->gallery as $g)
                        @php
                            $gUrl = (strpos($g,'http')===0) ? $g : (file_exists(public_path($g)) ? asset($g) : (file_exists(public_path('storage/'.$g)) ? asset('storage/'.$g) : (file_exists(public_path('cooperative_galleries/'.basename($g))) ? asset('cooperative_galleries/'.basename($g)) : $g)));
                        @endphp
                        <div class="gallery-thumb" style="width:92px;position:relative;">
                            <div style="width:92px;height:66px;overflow:hidden;border-radius:6px;border:1px solid rgba(0,0,0,0.06);">
                                <img src="{{ $gUrl }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                            </div>
                            <form method="POST" action="{{ route('admin.cooperatives.gallery.delete', $cooperative) }}" class="gallery-delete-form" style="position:absolute; top:4px; right:4px;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="path" value="{{ $g }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger gallery-delete-btn" style="padding:0.15rem 0.36rem;line-height:1;">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery_files[]" multiple accept="image/*" class="form-control">
            <div class="form-text">Upload additional images to append to the gallery.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Info</label>
            <textarea name="contact_info" class="form-control" rows="3">{{ old('contact_info', $p->contact_info ?? '') }}</textarea>
        </div>
        <hr>
        <button class="btn btn-primary">Save</button>
    </form>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.addEventListener('submit', function(e){
        var form = e.target;
        if(!form || !form.classList) return;
        if(form.classList.contains('gallery-delete-form')){
            e.preventDefault();
            // if appConfirm showed a confirm modal it will call submit(); this handler still runs.
            var path = form.querySelector('input[name="path"]').value;
            var action = form.action;
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            // find the thumbnail container to remove on success
            var thumb = form.closest('.gallery-thumb');

            fetch(action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ path: path })
            }).then(function(r){
                return r.json().catch(function(){ return { success: false, message: 'Server error' }; });
            }).then(function(json){
                if(json && json.success){
                    if(thumb) thumb.remove();
                    // show toast using existing session toast element
                    try{
                        var toastEl = document.getElementById('sessionToast');
                        var toastBody = document.getElementById('sessionToastBody');
                        toastEl.className = 'toast align-items-center border-0 text-bg-success';
                        toastBody.textContent = json.message || 'Image removed';
                        new bootstrap.Toast(toastEl, { autohide:true, delay:2000 }).show();
                    }catch(e){ alert(json.message || 'Removed'); }
                } else {
                    try{
                        var toastEl = document.getElementById('sessionToast');
                        var toastBody = document.getElementById('sessionToastBody');
                        toastEl.className = 'toast align-items-center border-0 text-bg-danger';
                        toastBody.textContent = (json && json.message) ? json.message : 'Could not remove image';
                        new bootstrap.Toast(toastEl, { autohide:true, delay:4000 }).show();
                    }catch(e){ alert((json && json.message) ? json.message : 'Could not remove image'); }
                }
            }).catch(function(){
                alert('Network error while deleting image.');
            });
        }
    }, true);
});
</script>
    
    
</div>
@endsection
