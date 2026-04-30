@extends('layouts.app')

@section('content')
<div class="py-4">

    <div class="mb-3">
        <a href="{{ route('videos.index') }}" class="btn btn-outline-danger d-inline-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" style="margin-right:8px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to videos
        </a>
    </div>

    <h1 class="mt-3">{{ $video->title }}</h1>
    <p class="text-muted">{{ optional($video->created_at)->toDayDateTimeString() }}</p>

    <div class="ratio ratio-16x9 mb-3 position-relative" style="background:#000; border-radius:8px;">

        @if($video->youtube_id)
            <div id="yt-player-wrap" style="position:relative;width:100%;height:100%;overflow:hidden;">

                <!-- YouTube Player -->
                <div id="ytplayer" style="width:100%;height:100%;position:absolute;top:0;left:0;z-index:1;"></div>

                <!-- FULL BLOCKING MASK - moved to body and positioned over player by script -->
                <div id="videoMask"
                     style="
                        position:fixed; /* will be updated by JS */
                        top:0;left:0;width:100%;height:100%;
                        z-index:2147483647;
                        pointer-events:auto;
                        background: transparent; /* keep transparent by default */
                        display:flex;
                        align-items:center;
                        justify-content:center;
                     ">

                    <button id="videoMaskBtn"
                            style="
                                background:rgba(var(--primary-r),0.97);
                                border:none;
                                color:#fff;
                                padding:20px 32px;
                                border-radius:9999px;
                                font-size:32px;
                                cursor:pointer;
                                box-shadow:0 12px 40px rgba(0,0,0,0.5);
                                transition:all 0.3s;
                                z-index:2147483648;
                            ">
                        ▶
                    </button>

                </div>

                <!-- Small invisible blocker over the iframe progress bar area -->
                <div id="videoProgressBlock" style="position:fixed;left:0;top:0;width:0;height:0;pointer-events:auto;z-index:2147483647;background:transparent;">
                </div>

            </div>

            <input type="hidden" id="yt-video-id" value="{{ $video->youtube_id }}">

        @else
            <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height:100%; border-radius:8px;">
                No preview available
            </div>
        @endif

    </div>

    <div class="mb-3">
        <div class="progress" style="height:10px">
            <div id="videoProgressBar" class="progress-bar" role="progressbar" style="width:0%"></div>
        </div>
        <div class="small text-muted mt-1">
            Progress: <span id="videoProgressPercent">0%</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {!! nl2br(e($video->description)) !!}
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const ytIdEl = document.getElementById('yt-video-id');
    if(!ytIdEl) return;

    const videoId = ytIdEl.value;
    const progressBar = document.getElementById('videoProgressBar');
    const progressText = document.getElementById('videoProgressPercent');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const ALLOWED_FORWARD_GAP = 2;

    // Load YouTube API
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    document.body.appendChild(tag);

    let ytApiReady = false;
    window.onYouTubeIframeAPIReady = () => ytApiReady = true;

    let player;

    // === EXTREMELY AGGRESSIVE BLOCKING ===
    (function(){
        const css = `
            #ytplayer, #ytplayer iframe,
            #ytplayer .ytp-chrome-bottom, #ytplayer .ytp-progress-bar-container,
            #ytplayer .ytp-controls, #ytplayer .ytp-settings-button,
            #ytplayer .ytp-chrome-top, #ytplayer .ytp-gradient-bottom,
            #ytplayer .ytp-pause-overlay, #ytplayer .ytp-share-button,
            #ytplayer .ytp-watch-later-button, .ytp-more-videos,
            .ytp-watermark, .ytp-impression-link, .ytp-title-text,
            .ytp-title-channel, .ytp-endscreen-content, .ytp-tooltip,
            .ytp-volume-slider, .ytp-time-display, .ytp-play-button {
                pointer-events: none !important;
                user-select: none !important;
                -webkit-user-select: none !important;
            }

            #videoMask { z-index: 99999999 !important; }
            #videoMask button { z-index: 100000000 !important; }

            /* Force hide control elements */
            .ytp-chrome-bottom, .ytp-controls, .ytp-progress-bar-container {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                height: 0 !important;
            }
        `;
        const style = document.createElement('style');
        style.textContent = css;
        document.head.appendChild(style);
    })();

    function createPlayerAndPlay(){
        if(player) return;

        if(!ytApiReady){
            setTimeout(createPlayerAndPlay, 100);
            return;
        }

        player = new YT.Player('ytplayer', {
            videoId: videoId,
            playerVars: {
                'enablejsapi': 1,
                'origin': window.location.origin,
                'controls': 0,
                'disablekb': 1,
                'rel': 0,
                'modestbranding': 1,
                'autoplay': 1,
                'fs': 0,
                'iv_load_policy': 3,
                'playsinline': 1,
                'html5': 1,
                'showinfo': 0
            },
            events: {
                'onReady': function(e){
                    try { e.target.playVideo(); } catch(err){}
                    setTimeout(blockAllClicks, 400);
                    setTimeout(blockAllClicks, 1200);
                    setTimeout(blockAllClicks, 2500);
                },
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function blockAllClicks(){
        const wrap = document.getElementById('yt-player-wrap');
        const iframe = document.querySelector('#ytplayer iframe');

        if(iframe) {
            iframe.style.pointerEvents = 'none';
            iframe.setAttribute('inert', 'true');   // Modern blocking
        }

        // Block every YouTube UI element inside the player
        document.querySelectorAll('#ytplayer *, .ytp-*').forEach(el => {
            if (el.className && /ytp/i.test(el.className)) {
                el.style.pointerEvents = 'none';
                el.setAttribute('inert', 'true');
            }
        });

        if(wrap) wrap.setAttribute('inert', 'true'); // extra safety
    }

    // Position the mask as a fixed overlay above the player so it cannot be bypassed by iframe overlays.
    const mask = document.getElementById('videoMask');
    const maskBtnEl = document.getElementById('videoMaskBtn');
    const progressBlock = document.getElementById('videoProgressBlock');
    function positionMask(){
        try{
            const wrap = document.getElementById('yt-player-wrap');
            if(!wrap || !mask) return;
            const rect = wrap.getBoundingClientRect();
            // move mask to body (so it sits above any player DOM) and set fixed positioning
            if(mask.parentElement !== document.body){ document.body.appendChild(mask); }
            mask.style.position = 'fixed';
            mask.style.left = rect.left + 'px';
            mask.style.top = rect.top + 'px';
            mask.style.width = rect.width + 'px';
            mask.style.height = rect.height + 'px';
            mask.style.background = 'transparent';
            mask.style.display = 'flex';
            mask.style.pointerEvents = 'auto';
            mask.style.touchAction = 'none';
            // center the button
            if(maskBtnEl){
                maskBtnEl.style.position = 'absolute';
                maskBtnEl.style.left = (rect.width/2 - (maskBtnEl.offsetWidth/2)) + 'px';
                maskBtnEl.style.top = (rect.height/2 - (maskBtnEl.offsetHeight/2)) + 'px';
                maskBtnEl.style.zIndex = 2147483648;
            }
            // position small blocker over the bottom progress area (approx 48px tall)
            if(progressBlock){
                const pbHeight = Math.min(60, Math.round(rect.height * 0.12));
                progressBlock.style.left = rect.left + 'px';
                progressBlock.style.top = (rect.top + rect.height - pbHeight) + 'px';
                progressBlock.style.width = rect.width + 'px';
                progressBlock.style.height = pbHeight + 'px';
                progressBlock.style.pointerEvents = 'auto';
                progressBlock.style.background = 'transparent';
                progressBlock.style.zIndex = 2147483647;
            }
        }catch(e){}
    }

    // update mask on resize/scroll
    window.addEventListener('resize', positionMask);
    window.addEventListener('scroll', positionMask, {passive:true});
    // also position at intervals for devices that don't fire resize reliably
    const posInterval = setInterval(positionMask, 500);
    setTimeout(()=> clearInterval(posInterval), 5000);

    // Observe mutations inside the player container and re-apply blocking
    let playerObserver = null;
    function startPlayerObserver(){
        try{
            const target = document.getElementById('yt-player-wrap');
            if(!target || playerObserver) return;
            playerObserver = new MutationObserver(() => {
                try{ blockAllClicks(); }catch(e){}
            });
            playerObserver.observe(target, { childList: true, subtree: true, attributes: true });
            // also run a short-lived interval to reinforce blocking for dynamic UI
            let runs = 0;
            const reinforce = setInterval(()=>{
                try{ blockAllClicks(); }catch(e){}
                runs++; if(runs>20) clearInterval(reinforce);
            }, 500);
        }catch(e){}
    }

    // block interactions on the specific progress block as well
    if(progressBlock){
        const blockHandler = (e)=>{ e.stopImmediatePropagation(); e.preventDefault(); };
        ['touchstart','pointerdown','mousedown','click','touchend','pointerup','mouseup'].forEach(ev=>{
            progressBlock.addEventListener(ev, blockHandler, {passive:false, capture:true});
        });
    }

    // ensure the mask is positioned immediately and when player is created
    setTimeout(positionMask, 100);
    setTimeout(positionMask, 400);
    setTimeout(positionMask, 1200);

    // Progress logic
    let watchSet = new Set();
    let highestWatched = -1;
    let lastSample = 0;
    let pollTimer = null;
    let sendTimer = null;

    function sampleAndMark(){
        try {
            const t = Math.floor(player.getCurrentTime() || 0);

            if(t > lastSample + 5 && t > highestWatched + ALLOWED_FORWARD_GAP){
                player.seekTo(Math.max(0, highestWatched || 0), true);
                showNotice('Skipping ahead is disabled.');
                lastSample = t;
                return;
            }

            if(t <= highestWatched + ALLOWED_FORWARD_GAP){
                watchSet.add(t);
                if(t > highestWatched) highestWatched = t;
            }

            lastSample = t;

            const duration = Math.max(1, Math.floor(player.getDuration() || 0));
            const percent = Math.min(100, Math.round((watchSet.size / duration) * 100));

            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';

            scheduleSend();
        } catch(e){}
    }

    function scheduleSend(){
        clearTimeout(sendTimer);
        sendTimer = setTimeout(sendProgress, 3000);
    }

    function sendProgress(){
        clearTimeout(sendTimer);
        const duration = Math.max(1, Math.floor(player.getDuration() || 0));
        const percent = Math.min(100, Math.round((watchSet.size / duration) * 100));

        fetch('/videos/{{ $video->id }}/progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ percent: percent })
        }).catch(() => {});
    }

    function onPlayerStateChange(event){
        const YTState = YT.PlayerState;

        if(event.data === YTState.PLAYING){
            if(pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(sampleAndMark, 1000);
            setTimeout(blockAllClicks, 500);   // re-block after playing
        }
        else if(event.data === YTState.PAUSED || event.data === YTState.ENDED){
            if(pollTimer) clearInterval(pollTimer);
            sendProgress();
            blockAllClicks();
        }
    }

    function showNotice(msg){
        let n = document.getElementById('videoNotice');
        if(!n){
            n = document.createElement('div');
            n.id = 'videoNotice';
            n.style.cssText = `position:absolute; left:50%; transform:translateX(-50%); bottom:150px;
                               background:rgba(0,0,0,0.9); color:#fff; padding:10px 20px; border-radius:8px;
                               z-index:20000000; font-size:0.95rem; white-space:nowrap;`;
            document.getElementById('yt-player-wrap').appendChild(n);
        }
        n.textContent = msg;
        setTimeout(() => { if(n) n.style.opacity = '0'; }, 2800);
    }

    // Mask Button
    const maskBtn = document.getElementById('videoMaskBtn');
    maskBtn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();

        if(!player){
            createPlayerAndPlay();
            return;
        }

        const state = player.getPlayerState();
        if(state === YT.PlayerState.PLAYING){
            player.pauseVideo();
        } else {
            player.playVideo();
        }
    });

});
</script>

@endsection