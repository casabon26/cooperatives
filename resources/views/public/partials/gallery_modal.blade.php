<div class="text-center">
  <style>
    .gallery-image-container{overflow:auto;max-height:70vh}
    .gallery-image-container img{cursor:zoom-in;max-width:100%;height:auto;display:block;margin:0 auto;}
    .gallery-image-container img.zoomed{cursor:zoom-out;max-width:none;max-height:none;width:auto;height:auto}
    .gallery-zoom-hint{font-size:0.85rem;color:#6c757d}
  </style>

  @if(!empty($gallery->image_url))
    <div class="gallery-image-container mb-3">
      <img src="{{ $gallery->image_url }}" alt="{{ $gallery->alt_text ?: $gallery->title }}" data-gallery-modal-image>
    </div>
    <div class="gallery-zoom-hint mb-2">Click image to zoom</div>
  @endif

  @if($gallery->title)
    <h5 class="mb-1">{{ $gallery->title }}</h5>
  @endif
  @if($gallery->description)
    <div class="small text-muted">{{ $gallery->description }}</div>
  @endif
</div>
