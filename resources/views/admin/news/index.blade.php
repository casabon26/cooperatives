@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h4>Manage Latest Updates</h4>
            {{-- Inline static flash messages removed; layout provides dynamic popup instead. --}}

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Create Article</h5>
                    <form method="POST" action="/admin/manage-news" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (optional)</label>
                            <input type="file" name="image" accept="image/*" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Homepage Card Slot (optional)</label>
                            <input type="number" name="card_slot" id="card_slot_input" min="1" max="20" class="form-control" placeholder="Enter slot number (e.g. 1)">
                            <input type="hidden" name="confirm_overwrite" id="confirm_overwrite_input" value="0">
                            <div class="form-text">Assign a numeric slot to show this article as a homepage card. Leave empty for none.</div>
                        </div>
                        <button class="btn btn-danger">Create</button>
                    </form>
                </div>
            </div>

            <div class="list-group">
                @foreach($news as $n)
                    <div class="list-group-item d-flex gap-3">
                        <div style="width:120px">
                            @php
                                $imgUrl = null;
                                if($n->image){
                                    $storagePath = public_path('storage/'.$n->image);
                                    $directPath = public_path($n->image);
                                    $publicNewsPath = public_path('news/'.basename($n->image));
                                    if(file_exists($storagePath)){
                                        $imgUrl = asset('storage/'.$n->image);
                                    } elseif(file_exists($directPath)){
                                        $imgUrl = asset($n->image);
                                    } elseif(file_exists($publicNewsPath)){
                                        $imgUrl = asset('news_images/'.basename($n->image));
                                    }
                                }
                            @endphp
                            @if($n->image_data)
                                <img src="data:{{ $n->image_mime }};base64,{{ $n->image_data }}" class="img-fluid rounded">
                            @elseif($imgUrl)
                                <img src="{{ $imgUrl }}" class="img-fluid rounded">
                            @else
                                <div class="bg-light border" style="height:80px;display:flex;align-items:center;justify-content:center">No image</div>
                            @endif
                        </div>
                        <div class="flex-fill">
                            <h6 class="mb-1">{{ $n->title }}</h6>
                            <div class="small text-muted">{{ optional($n->published_at)->toDateString() }}</div>
                            <p class="mb-1 small">{{ \Illuminate\Support\Str::limit(strip_tags($n->content), 160) }}</p>
                            <div class="mt-2">
                                <a href="/admin/manage-news/{{ $n->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="/admin/manage-news/{{ $n->id }}/delete" style="display:inline" data-confirm="Delete this article?">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $news->links() }}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function(){
        // occupied slots map from current news items
        const occupied = {
            @foreach($news as $n)
                @if($n->card_slot)
                    '{{ $n->card_slot }}': {!! json_encode($n->title) !!},
                @endif
            @endforeach
        };

        const form = document.querySelector('form[action="/admin/manage-news"]');
        if(!form) return;
        const slotInput = document.getElementById('card_slot_input');
        const confirmInput = document.getElementById('confirm_overwrite_input');

        form.addEventListener('submit', function(e){
            const v = slotInput && slotInput.value ? slotInput.value.trim() : '';
            if(!v) return;
            if(occupied[v]){
                // prevent immediate submit and show styled confirm modal
                e.preventDefault();
                window.appConfirm('Card '+v+' is currently assigned to "'+occupied[v]+'". Overwrite?').then(function(ok){
                    if(!ok) return;
                    if(confirmInput) confirmInput.value = '1';
                    form.submit();
                });
            }
        }, false);
    })();
</script>
@endsection
