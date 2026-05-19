@extends('layouts.app')

@section('back-button')
  @include('partials.back-button', ['label' => 'Back to News', 'url' => url('/news'), 'class' => ''])
@endsection

@section('hero')
  {{-- Hero intentionally minimal so back-button appears above the title and image --}}
  <div class="news-hero py-2" style="background:transparent"></div>
@endsection

@section('content')
  <div class="py-4">
    <div class="container">
      @php
        $imgUrl = null;
        if($news->image){
          $storagePath = public_path('storage/'.$news->image);
          $directPath = public_path($news->image);
          $publicNewsPath = public_path('assets/images/news/'.basename($news->image));
          if(file_exists($storagePath)){
            $imgUrl = asset('storage/'.$news->image);
          } elseif(file_exists($directPath)){
            $imgUrl = asset($news->image);
          } elseif(file_exists($publicNewsPath)){
            $imgUrl = asset('assets/images/news/'.basename($news->image));
          }
        }
      @endphp

      {{-- Title placed here so it appears below back-button and above the image --}}
      <h1 class="mb-2">{{ $news->title }}</h1>
      <div class="text-muted small mb-3">{{ optional($news->published_at ?? $news->created_at)->toDayDateTimeString() }}</div>

      @if($imgUrl)
        <div class="news-hero-img mb-3" data-bg="{{ $imgUrl }}" style="height:420px; background-size:cover; background-position:center; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.08)"></div>
      @endif

      <div class="article-body">
        <div class="card p-4">
          <div class="card-body">
            {!! nl2br(e($news->description ?? $news->summary ?? strip_tags($news->content))) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const hero = document.querySelector('.news-hero-img[data-bg]');
  if(hero && 'IntersectionObserver' in window){
    const obs = new IntersectionObserver(function(entries, o){
      entries.forEach(function(e){ if(e.isIntersecting){ hero.style.backgroundImage = `url('${hero.dataset.bg}')`; hero.removeAttribute('data-bg'); o.unobserve(hero); }});
    }, {rootMargin:'200px'});
    obs.observe(hero);
  } else if(hero){ hero.style.backgroundImage = `url('${hero.dataset.bg}')`; hero.removeAttribute('data-bg'); }
});
</script>
<style>
  .article-body img{ max-width:100%; height:auto; display:block; margin:1rem 0 }
  .article-body p{ font-size:1.03rem; line-height:1.8; color:#222 }
  .article-body blockquote{ border-left:4px solid rgba(200,16,46,0.12); padding-left:16px; color:#666; font-style:italic }
  .news-hero{ background-attachment:fixed }
  @media (max-width:768px){ .news-hero-img{ height:220px } }
</style>
@endsection
