// Small theme interactions for Cooperative Portal
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(function(a){
      a.addEventListener('click', function(e){
        var href = a.getAttribute('href');
        if(href && href.length>1){
          var el = document.querySelector(href);
          if(el){
            e.preventDefault();
            el.scrollIntoView({behavior:'smooth',block:'start'});
          }
        }
      });
    });

    // Simple card hover elevation (for JS-enabled enhancement)
    document.querySelectorAll('.card').forEach(function(card){
      card.addEventListener('mouseenter', function(){
        card.style.transition = 'transform .15s ease, box-shadow .15s ease';
        card.style.transform = 'translateY(-6px)';
        card.style.boxShadow = '0 14px 34px rgba(11,37,77,0.08)';
      });
      card.addEventListener('mouseleave', function(){
        card.style.transform = '';
        card.style.boxShadow = '';
      });
    });

    // Make external links open in new tab when not already set (but skip links pointing to the
    // same host).  The previous selector was too broad and treated absolute URLs for our own
    // routes as "external" when the browser resolves them to e.g.
    // http://localhost/admin/… – causing size filter buttons to open in a new tab.
    document.querySelectorAll('a[href^="http"]:not([target])').forEach(function(link){
      try {
        var url;
        try { url = new URL(link.href); } catch(_) { return; }
        // only mark as external if the host is different from the current page
        if (url.host !== window.location.host) {
          link.setAttribute('target','_blank');
          link.setAttribute('rel','noopener noreferrer');
        }
      } catch(e) { /* ignore */ }
    });

  });
})();
