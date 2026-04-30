@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">{{ $video->title }}</h1>
        <a href="{{ route('training.index') }}" class="btn btn-secondary">Back to Training</a>
    </div>
    <p class="text-muted">{{ $video->description }}</p>


    <div class="mb-3">
        @if($video->file_path)
            <!-- Self-hosted Video - Fullscreen disabled + double-click blocked -->
            <div class="training-player" style="max-width:900px; position:relative;">
                  <video id="trainingVideo" 
                      controls 
                      controlsList="nofullscreen" 
                      style="width:100%; background:#000;" 
                      playsinline webkit-playsinline 
                      preload="metadata">
                    <source src="{{ asset('storage/'.$video->file_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <!-- Circular loader tied to play/finish -->
                <div id="videoLoader" class="video-loader" style="display:none;">
                    <div class="spinner" id="videoSpinner"></div>
                    <div class="loader-label" id="videoLoaderLabel">Loading...</div>
                </div>
                <!-- Mask to cover progress bar -->
                 @can('access-admin')
                 <div id="videoMask" 
                     title="Admin: seeking allowed"
                     style="position:absolute; left:0; right:0; bottom:0; height:110px; 
                           background:rgba(0,0,0,0.05); z-index:2147483647; pointer-events:none; cursor:default;">
                 </div>
                 @else
                 <div id="videoMask" 
                     title="Seeking is disabled for this training"
                     style="position:absolute; left:0; right:0; bottom:60px; height:48px; 
                           background:rgba(0,0,0,0.02); z-index:2147483647; pointer-events:auto; cursor:not-allowed;">
                 </div>
                 @endcan
            </div>
            
        @elseif($video->youtubeId())
            <!-- YouTube Video -->
            <div class="training-player" style="max-width:900px; position:relative;">
                <div class="ratio ratio-16x9">
                    <iframe id="youtubePlayer" 
                        src="https://www.youtube.com/embed/{{ $video->youtubeId() }}?rel=0&controls=1&disablekb=1&enablejsapi=1&origin={{ urlencode(request()->getSchemeAndHttpHost()) }}" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        style="background:#000; width:100%; height:100%;">
                    </iframe>
                </div>
                <!-- Circular loader tied to play/finish -->
                <div id="videoLoader" class="video-loader" style="display:none;">
                    <div class="spinner" id="videoSpinner"></div>
                    <div class="loader-label" id="videoLoaderLabel">Loading...</div>
                </div>
                @can('access-admin')
                <div id="ytMask" 
                    title="Admin: seeking allowed"
                    style="position:absolute; left:0; right:0; bottom:0; height:125px; 
                        background:rgba(0,0,0,0.03); z-index:2147483647; pointer-events:none; cursor:default;">
                </div>
                @else
                <div id="ytMask" 
                    title="Seeking is disabled for this training"
                    style="position:absolute; left:0; right:0; bottom:60px; height:48px; 
                        background:rgba(0,0,0,0.02); z-index:2147483647; pointer-events:auto; cursor:not-allowed;">
                </div>
                @endcan
            </div>
            
            <div class="d-flex justify-content-center my-2">
                <div class="alert alert-info mb-0" style="font-size:1.08em;">
                    <span style="font-weight:600;">Click the video to start playback. Progress will update as you watch.</span>
                </div>
            </div>
        @else
            <div class="alert alert-secondary">No video available for this training.</div>
        @endif
    </div>

    @auth
        <div id="completion-section">
            @if($completed)
                <div class="alert alert-success">You have completed this training. <a href="#">View certificate</a></div>
            @else
                
                <form id="completeForm" method="POST" action="{{ route('training.complete', $video) }}">
                    @csrf
                    <button id="completeBtn" type="submit" class="btn btn-certificate" disabled>
                        <span style="display:inline-block;vertical-align:middle;margin-right:0.5em;">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                                <rect x="4" y="5.5" width="14" height="11" rx="2" fill="#fff" fill-opacity="0.13" stroke="#fff" stroke-width="1.2"/>
                                <rect x="7" y="8" width="8" height="1.5" rx="0.75" fill="#fff" fill-opacity="0.7"/>
                                <rect x="7" y="11" width="5" height="1.2" rx="0.6" fill="#fff" fill-opacity="0.5"/>
                                <path d="M11 16.5l-1.2 2.2c-.13.24.13.5.38.38l1.32-.66 1.32.66c.25.12.51-.14.38-.38L13 16.5" fill="#B82132"/>
                                <circle cx="11" cy="15.5" r="1.2" fill="#B82132" fill-opacity="0.9" stroke="#fff" stroke-width="0.7"/>
                            </svg>
                        </span>
                        Mark as Completed & Get Certificate
                    </button>
                </form>
            @endif
        </div>
    @else
        <p>Please <a href="/user/login">log in</a> to mark completion and get a certificate.</p>
    @endauth

</div>
@endsection

@push('styles')
<style>
    /* Hide fullscreen button where possible */
    #trainingVideo::-webkit-media-controls-fullscreen-button,
    video::-webkit-media-controls-fullscreen-button {
        display: none !important;
    }

    /* Hide progress bar */
    #trainingVideo::-webkit-media-controls-timeline,
    #trainingVideo::-webkit-media-controls-progress-bar {
        display: none !important;
    }

    /* Prevent double-tap zoom gestures on mobile where possible */
    .training-player, .training-player video, .training-player iframe {
        touch-action: manipulation;
        -ms-touch-action: manipulation;
    }
    /* Temporarily hide the percent progress UI while monitoring approach is reconsidered */
    #training-progress-container { display: none !important; }
    #training-progress-label { display: none !important; }

    /* Video loader overlay */
    .video-loader {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        z-index: 999999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .spinner {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 6px solid rgba(255,255,255,0.15);
        border-top-color: #ff4b5c;
        animation: spin 1s linear infinite;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        margin-bottom: 8px;
    }
    .loader-label { color: #fff; background: rgba(0,0,0,0.45); padding:6px 10px; border-radius:6px; font-weight:600; font-size:0.95rem; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg);} }
</style>
@endpush

@push('scripts')
(if(typeof window !== 'undefined'){
    var s = document.createElement('script');
    s.src = 'https://www.youtube.com/iframe_api';
    s.async = true;
    document.head.appendChild(s);
})
<script>
(function(){
    let videoCompleted = false;
    let emailSent = false; // track whether we've fired the quick email
    const videoLength = {{ $video->length ?? 'null' }};

    // ==================== HTML5 Video (native play allowed) ====================
    const htmlVideo = document.getElementById('trainingVideo');
    const progressBar = document.getElementById('training-progress-bar');
    const progressLabel = document.getElementById('training-progress-label');
    if (htmlVideo) {
        let supposedCurrentTime = 0;
        let htmlMax = 0;

        // Block double-click / double-tap from entering fullscreen
        htmlVideo.addEventListener('dblclick', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
        });
        (function(){
            let lastTap = 0;
            htmlVideo.addEventListener('touchend', function(e){
                const now = Date.now();
                if (now - lastTap <= 300) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
                lastTap = now;
            }, {passive:false});
        })();

        htmlVideo.addEventListener('timeupdate', function() {
            if (!htmlVideo.seeking) {
                supposedCurrentTime = htmlVideo.currentTime;
            }
            htmlMax = Math.max(htmlMax, htmlVideo.currentTime || 0);
            // Use DB length if available, else fallback to video duration
            let duration = videoLength || htmlVideo.duration;
            let percent = duration ? Math.min(100, Math.round((htmlMax / duration) * 100)) : 0;
            if (progressBar) progressBar.style.width = percent + '%';
            if (progressLabel) progressLabel.textContent = percent + '%';
            if (duration && (htmlMax / duration >= 0.95 || htmlVideo.currentTime > duration - 3)) {
                videoCompleted = true;
                enableCompleteButton();
                if (!emailSent) {
                    emailSent = true;
                    console.log('Reached >=95% (HTML5). Sending quick email.');
                    fetch('/api/send-completion-email', { method:'POST', credentials:'same-origin', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                        .then(r=>r.json()).then(j=>console.log('Email send response', j)).catch(e=>console.log('Email send error', e));
                }
            }
        });
        htmlVideo.addEventListener('seeking', function() {
            const delta = htmlVideo.currentTime - supposedCurrentTime;
            if (delta > 0.5) {
                htmlVideo.currentTime = supposedCurrentTime;
            }
        });
        htmlVideo.addEventListener('playing', function() {
            console.log('HTML5 playing - hide loader');
            setTimeout(hideVideoLoader, 600);
        });

        htmlVideo.addEventListener('play', function() {
            console.log('HTML5 play event - show loader');
            showVideoLoader('Loading...');
        });

        htmlVideo.addEventListener('ended', function() {
            console.log('HTML5 ended - done');
            videoCompleted = true;
            enableCompleteButton();
            setVideoLoaderDone();
        });
    }

    // ==================== YouTube Video (IFrame API + custom progress) ====================
    (function(){
        const ytContainer = document.getElementById('youtubePlayer');
        if (!ytContainer) return;

        // Create player when API is ready
        window.onYouTubeIframeAPIReady = function() {
            try {
                console.log('YouTube API ready');
                const videoId = {{ json_encode($video->youtubeId()) }};
                const userId = {{ json_encode(Auth::id()) }};
                const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

                const player = new YT.Player('youtubePlayer', {
                    videoId: videoId,
                    playerVars: { controls: 1, disablekb: 1, rel: 0, modestbranding: 1 },
                    events: {
                        onReady: function() {
                            console.log('Player Ready');
                            // show custom UI
                            const wrap = document.getElementById('yt_custom_progress'); if (wrap) wrap.style.display = 'block';

                            // Resume support
                            (async function(){
                                try {
                                    const res = await fetch('/api/get-video-progress?videoId=' + encodeURIComponent(videoId), { credentials: 'same-origin' });
                                    if (res.ok) {
                                        const j = await res.json();
                                        if (j && j.currentTime) {
                                            console.log('Resuming at', j.currentTime);
                                            try { player.seekTo(parseFloat(j.currentTime) || 0, true); } catch(e){}
                                        }
                                    }
                                } catch(e){ console.log('resume fetch error', e); }
                            })();

                            // center click toggles play/pause for masked users
                            const center = document.getElementById('ytCenterClick');
                            if (center) center.addEventListener('click', function(e){ e.stopPropagation(); try{ const s = player.getPlayerState(); if (s===YT.PlayerState.PLAYING) player.pauseVideo(); else player.playVideo(); }catch(e){} }, {passive:false});

                            // Poll every 3s while playing
                            let lastSavedPercent = 0;
                            const poll = setInterval(async function(){
                                try {
                                    const state = player.getPlayerState();
                                    if (state !== YT.PlayerState.PLAYING) return;
                                    const t = player.getCurrentTime();
                                    const dur = player.getDuration() || 0;
                                    const percent = dur ? Math.round((t/dur)*100) : 0;

                                    const bar = document.getElementById('yt_progress_bar'); if (bar) { bar.style.transition='width .6s ease'; bar.style.width = percent + '%'; }
                                    const label = document.getElementById('yt_progress_label'); if (label) label.textContent = 'Progress: ' + percent + '% watched';
                                    console.log('Progress Updated:', percent + '%');

                                        if (percent >= 95 && !emailSent) {
                                            emailSent = true;
                                            console.log('Reached >=95% (YouTube). Sending quick email.');
                                            try {
                                                await fetch('/api/send-completion-email', { method:'POST', credentials:'same-origin', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken }});
                                                console.log('Email send triggered');
                                            } catch(e) { console.log('email send failed', e); }
                                            enableCompleteButton();
                                        }

                                    if (percent >= lastSavedPercent + 5) {
                                        lastSavedPercent = percent;
                                        try {
                                            await fetch('/api/save-video-progress', {
                                                method:'POST',
                                                headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                                                body: JSON.stringify({ userId: userId, videoId: videoId, progressPercent: percent, currentTime: t, totalDuration: dur })
                                            });
                                            console.log('Progress saved:', percent + '%');
                                        } catch(e){ console.log('save progress failed', e); }
                                    }
                                } catch(e){ console.log('poll error', e); }
                            }, 3000);

                            // expose for state handler
                            player._progressPoll = poll;
                            window._trainingYT = player;
                        },
                        onStateChange: async function(evt){
                            try {
                                const state = evt.data;
                                const t = (typeof window._trainingYT.getCurrentTime === 'function') ? window._trainingYT.getCurrentTime() : 0;
                                const dur = (typeof window._trainingYT.getDuration === 'function') ? window._trainingYT.getDuration() : 0;
                                if (state === YT.PlayerState.PAUSED) {
                                    const percent = dur ? Math.round((t/dur)*100) : 0;
                                    try {
                                        await fetch('/api/save-video-progress', {
                                            method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                                            body: JSON.stringify({ userId: userId, videoId: videoId, progressPercent: percent, currentTime: t, totalDuration: dur })
                                        });
                                        console.log('Progress saved on pause:', percent + '%');
                                    } catch(e){ console.log('save on pause failed', e); }
                                }
                                if (state === YT.PlayerState.ENDED) {
                                    try {
                                        await fetch('/api/mark-video-complete', {
                                            method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                                            body: JSON.stringify({ userId: userId, videoId: videoId })
                                        });
                                        console.log('Marked complete');
                                    } catch(e){ console.log('mark complete failed', e); }
                                    videoCompleted = true; enableCompleteButton();
                                    // mark loader done for youtube
                                    try { setVideoLoaderDone(); } catch(e){}
                                }
                                if (state === YT.PlayerState.PLAYING) {
                                    console.log('YouTube PLAYING - show then hide loader');
                                    try { showVideoLoader('Loading...'); } catch(e){}
                                    setTimeout(function(){ try{ hideVideoLoader(); }catch(e){} }, 700);
                                }
                            } catch(e){ console.log('state handler error', e); }
                        }
                    }
                });
            } catch(e){ console.log('YT init error', e); }
        };

        // If API already loaded call init
        if (typeof YT !== 'undefined' && typeof YT.Player !== 'undefined') {
            try { window.onYouTubeIframeAPIReady(); } catch(e) {}
        }

        // Developer hint: if using 127.0.0.1 YouTube embeds may fail — prefer http://localhost:8000
        if (location && location.hostname === '127.0.0.1') {
            console.warn('YouTube embeds may fail on raw IPs. Use http://localhost:8000 instead of 127.0.0.1');
            const note = document.createElement('div');
            note.style.background='#fff3cd'; note.style.border='1px solid #ffeeba'; note.style.padding='8px'; note.style.margin='8px 0'; note.style.borderRadius='6px';
            note.textContent = 'Dev note: YouTube embeds sometimes fail on raw IP addresses — use http://localhost:8000 for testing.';
            const c = document.querySelector('.container'); if (c) c.insertBefore(note, c.firstChild);
        }
    })();

    // Determine if current user is admin (server-provided flag)
    const isAdmin = @json(Auth::check() && Auth::user()->can('access-admin'));

    // ------- Video loader helpers -------
    function showVideoLoader(text){
        try{
            // pick the loader inside the currently visible .training-player
            const container = document.querySelector('.training-player');
            let loader = container ? container.querySelector('#videoLoader') : document.getElementById('videoLoader');
            if (!loader) loader = document.getElementById('videoLoader');
            if (!loader) return;
            const label = loader.querySelector('.loader-label');
            if (label && text) label.textContent = text;
            loader.style.display = 'flex';
        }catch(e){console.log('showVideoLoader error', e);}    
    }
    function hideVideoLoader(){
        try{
            const container = document.querySelector('.training-player');
            let loader = container ? container.querySelector('#videoLoader') : document.getElementById('videoLoader');
            if (!loader) loader = document.getElementById('videoLoader');
            if (!loader) return;
            loader.style.display = 'none';
        }catch(e){console.log('hideVideoLoader error', e);}    
    }
    function setVideoLoaderDone(){
        try{
            const container = document.querySelector('.training-player');
            let loader = container ? container.querySelector('#videoLoader') : document.getElementById('videoLoader');
            if (!loader) loader = document.getElementById('videoLoader');
            if (!loader) return;
            const spinner = loader.querySelector('.spinner'); if (spinner) spinner.style.display='none';
            const label = loader.querySelector('.loader-label'); if (label) label.textContent = 'Done';
            loader.style.display = 'flex';
            setTimeout(()=>{ try{ loader.style.display='none'; if (spinner) spinner.style.display='block'; if (label) label.textContent='Loading...'; }catch(e){} }, 2500);
        }catch(e){console.log('setVideoLoaderDone error', e);}    
    }

    // Attach mask/blocking listeners only for non-admin users
    if (!isAdmin) {
        try {
            const videoMask = document.getElementById('videoMask');
            if (videoMask) {
                ['pointerdown','pointerup','mousedown','mouseup','click','dblclick','contextmenu'].forEach(ev => {
                    videoMask.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); }, {passive:false});
                });
            }
            const ytMaskEl = document.getElementById('ytMask');
            if (ytMaskEl) {
                ['pointerdown','pointerup','mousedown','mouseup','click','dblclick','contextmenu'].forEach(ev => {
                    ytMaskEl.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); }, {passive:false});
                });
            }
        } catch(e) { console.log('Mask attach error', e); }

        // Prevent keyboard seeking on HTML5 video (left/right/home/end keys)
        if (htmlVideo) {
            htmlVideo.addEventListener('keydown', function(e){
                const keys = ['ArrowLeft','ArrowRight','Home','End','PageUp','PageDown'];
                if (keys.indexOf(e.key) !== -1) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            }, true);
        }
    }

    function enableCompleteButton() {
        const btn = document.getElementById('completeBtn');
        if (btn) {
            btn.disabled = false;
            try { btn.removeAttribute('disabled'); } catch(e){}
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            console.log('Complete button enabled');
        }
    }

})();
</script>
@endpush