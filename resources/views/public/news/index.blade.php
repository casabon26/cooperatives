@extends('layouts.app')

@section('hero')
  <div class="container mt-3">
    <div class="py-4">
      <h1 class="m-0">News</h1>
      <p class="text-muted">Latest updates and announcements</p>
    </div>
  </div>
@endsection

@section('content')
  <div class="py-4">
    <div id="newsList" class="row g-3">
      @include('public.news._list', ['news' => $news])
    </div>

    <div class="mt-4 text-center" id="newsPager">
      @if($news->hasMorePages())
        <button id="loadMoreNews" class="btn btn-outline-primary">Load more</button>
      @endif
    </div>
  </div>
@endsection

@section('styles')
  .news-figure{ height:140px; background-size:cover; background-position:center; border-radius:12px 12px 0 0 }
  .news-card{ display:flex; flex-direction:column; overflow:hidden }
  .btn-readmore{ background: linear-gradient(90deg,#C8102E,#E30613); color:#fff; border-radius:999px; padding:8px 14px }
  .news-title{ font-size:1.05rem; margin-bottom:0.25rem }
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const lazyBackgrounds = [].slice.call(document.querySelectorAll('.news-figure[data-bg]'));
  if('IntersectionObserver' in window && lazyBackgrounds.length){
    let bgObserver = new IntersectionObserver(function(entries, observer){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          const el = entry.target;
          const url = el.dataset.bg;
          if(url){ el.style.backgroundImage = `url('${url}')`; el.removeAttribute('data-bg'); }
          observer.unobserve(el);
        }
      });
    }, {rootMargin:'200px'});
    lazyBackgrounds.forEach(bg=>bgObserver.observe(bg));
    window.bgObserver = bgObserver;
  }

  const loadBtn = document.getElementById('loadMoreNews');
  if(loadBtn){
    let nextPage = 2;
    loadBtn.addEventListener('click', function(){
      const url = new URL(window.location.href);
      url.searchParams.set('page', nextPage);
      fetch(url.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>{ if(!r.ok) throw r; return r.text(); })
        .then(html=>{
          const container = document.getElementById('newsList');
          const wrapper = document.createElement('div');
          wrapper.innerHTML = html;
          while(wrapper.firstChild){ container.appendChild(wrapper.firstChild); }
          nextPage++;
          if(html.trim().length < 50) { loadBtn.style.display='none'; }
          const newLazy = [].slice.call(container.querySelectorAll('.news-figure[data-bg]'));
          newLazy.forEach(el=>{ if(window.bgObserver) window.bgObserver.observe(el); });
        }).catch(()=>{ loadBtn.textContent = 'Failed to load'; setTimeout(()=>loadBtn.textContent='Load more',1500); });
    });
  }
});
</script>
@endsection
