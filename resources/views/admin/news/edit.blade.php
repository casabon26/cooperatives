@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Article</h5>
                    <form method="POST" action="/admin/manage-news/{{ $news->id }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" value="{{ $news->title }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="6" required>{{ $news->content }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (optional)</label>
                            @php
                                $imgUrl = null;
                                if($news->image){
                                    $storagePath = public_path('storage/'.$news->image);
                                    $directPath = public_path($news->image);
                                    $publicNewsPath = public_path('news/'.basename($news->image));
                                    if(file_exists($storagePath)){
                                        $imgUrl = asset('storage/'.$news->image);
                                    } elseif(file_exists($directPath)){
                                        $imgUrl = asset($news->image);
                                    } elseif(file_exists($publicNewsPath)){
                                        $imgUrl = asset('assets/images/news/'.basename($news->image));
                                    }
                                }
                            @endphp
                            @if($news->image_data)
                                <div class="mb-2"><img src="data:{{ $news->image_mime }};base64,{{ $news->image_data }}" class="img-fluid rounded" style="max-height:160px"></div>
                            @elseif($imgUrl)
                                <div class="mb-2"><img src="{{ $imgUrl }}" class="img-fluid rounded" style="max-height:160px"></div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Homepage Card Slot (optional)</label>
                            <input type="number" name="card_slot" id="card_slot_input" min="1" max="20" value="{{ $news->card_slot }}" class="form-control">
                            <input type="hidden" name="confirm_overwrite" id="confirm_overwrite_input" value="0">
                            <div class="form-text">Assign a numeric slot to show this article as a homepage card. Leave empty for none.</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/news" class="btn btn-outline-secondary">Back</a>
                            <button class="btn btn-danger">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function(){
        const occupied = {
            @php
                $all = \App\Models\News::whereNotNull('card_slot')->where('id','<>',$news->id)->get();
            @endphp
            @foreach($all as $n)
                '{{ $n->card_slot }}': {!! json_encode($n->title) !!},
            @endforeach
        };

        const form = document.querySelector('form[action="/admin/manage-news/{{ $news->id }}"]');
        if(!form) return;
        const slotInput = document.getElementById('card_slot_input');
        const confirmInput = document.getElementById('confirm_overwrite_input');

        form.addEventListener('submit', function(e){
            const v = slotInput && slotInput.value ? slotInput.value.trim() : '';
            if(!v) return;
            if(occupied[v]){
                if(!confirm('Card '+v+' is currently assigned to "'+occupied[v]+'". Overwrite?')){
                    e.preventDefault();
                    return;
                }
                if(confirmInput) confirmInput.value = '1';
            }
        }, false);
    })();
</script>
@endsection
