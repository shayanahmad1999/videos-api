<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Advanced Video Player – Playlist</title>
    <style>
        :root {
            --bg: #0f0f0f;
            --panel: #1a1a1a;
            --accent: #ff3b30;
            --text: #fff;
            --muted: #bdbdbd;
            --track: #3a3a3a;
            --track-fill: #e0e0e0;
            --tooltip: #111;
            --border: #2a2a2a;
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            margin: 0;
            background: linear-gradient(135deg, #232526, #414345);
            color: var(--text);
            font-family: Inter, system-ui, Arial, sans-serif
        }

        .wrap {
            max-width: 1100px;
            margin: 48px auto;
            padding: 0 16px
        }

        .title {
            font-weight: 600;
            margin: 0 0 14px 2px
        }

        .layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px
        }

        @media(max-width:900px) {
            .layout {
                grid-template-columns: 1fr
            }
        }

        .player {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .45)
        }

        video {
            width: 100%;
            height: auto;
            display: block;
            background: #000
        }

        .scrim {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(to top, rgba(0, 0, 0, .55), rgba(0, 0, 0, 0) 45%);
            opacity: 0;
            transition: opacity .2s
        }

        .player:hover .scrim,
        .player.controls-visible .scrim {
            opacity: 1
        }

        .controls {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px 12px 12px;
            transform: translateY(14px);
            opacity: 0;
            transition: opacity .15s, transform .15s
        }

        .player:hover .controls,
        .player.controls-visible .controls {
            opacity: 1;
            transform: translateY(0)
        }

        .progress {
            position: relative;
            height: 6px;
            border-radius: 999px;
            background: var(--track);
            cursor: pointer
        }

        .progress .buffered,
        .progress .played {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            border-radius: 999px;
            width: 0%
        }

        .progress .buffered {
            background: #7a7a7a
        }

        .progress .played {
            background: var(--accent)
        }

        .progress .thumb {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 59, 48, .25);
            display: none
        }

        .progress:hover .thumb {
            display: block
        }

        .row {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: space-between
        }

        .left,
        .right {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .btn {
            appearance: none;
            border: 0;
            background: transparent;
            color: var(--text);
            padding: 8px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: background .15s, opacity .15s
        }

        .btn:hover {
            background: rgba(255, 255, 255, .08)
        }

        .btn:active {
            transform: scale(.98)
        }

        .btn svg {
            width: 22px;
            height: 22px;
            display: block
        }

        .btn[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translate(-50%, -6px);
            background: var(--tooltip);
            color: #eee;
            font-size: 12px;
            padding: 6px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity .12s
        }

        .btn:hover::after {
            opacity: 1
        }

        .time {
            font-variant-numeric: tabular-nums;
            font-size: 13px;
            color: var(--muted);
            padding: 0 6px
        }

        .vol {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 120px
        }

        .range {
            appearance: none;
            width: 120px;
            height: 4px;
            border-radius: 999px;
            background: var(--track);
            outline: none;
            cursor: pointer
        }

        .range::-webkit-slider-thumb {
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--track-fill)
        }

        .range::-moz-range-thumb {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--track-fill);
            border: 0
        }

        .range::-webkit-slider-runnable-track {
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(to right, var(--track-fill) var(--val, 50%), var(--track) 0)
        }

        .range::-moz-range-track {
            height: 4px;
            border-radius: 999px;
            background: var(--track)
        }

        .menu {
            position: relative
        }

        .menu-list {
            position: absolute;
            bottom: 110%;
            right: 0;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 6px;
            display: none;
            min-width: 160px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35)
        }

        .menu.open .menu-list {
            display: block
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer
        }

        .menu-item:hover {
            background: #2a2a2a
        }

        @media(max-width:640px) {
            .time {
                display: none
            }

            .vol .range {
                width: 90px
            }
        }

        .playlist {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden
        }

        .plist-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border)
        }

        .plist-title {
            font-weight: 600
        }

        .plist {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 520px;
            overflow: auto
        }

        .item {
            display: grid;
            grid-template-columns: 90px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            cursor: pointer
        }

        .item:hover {
            background: #212121
        }

        .thumb {
            width: 100%;
            height: 56px;
            border-radius: 10px;
            background: #111;
            object-fit: cover
        }

        .meta {
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .meta .name {
            font-size: 14px
        }

        .meta .id {
            font-size: 12px;
            color: var(--muted)
        }

        .playing-badge {
            font-size: 12px;
            color: #fff;
            background: var(--accent);
            padding: 2px 8px;
            border-radius: 999px
        }

        .item.active {
            background: #272727
        }

        .empty {
            padding: 18px;
            color: var(--muted);
            text-align: center
        }

        .toolbar {
            display: flex;
            gap: 8px;
            margin: 0 0 8px 2px
        }

        .toolbar input[type="search"] {
            flex: 1;
            min-width: 0;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #141414;
            color: #fff;
            padding: 10px 12px;
            outline: none
        }

        .toolbar button {
            border-radius: 10px;
            background: #222;
            color: #fff;
            border: 1px solid var(--border);
            padding: 10px 12px;
            cursor: pointer
        }

        .toolbar button:hover {
            background: #272727
        }
    </style>
</head>

<body>
    <div class="wrap" id="playlistRoot" data-playlist-url="{{ url('/api/videos') }}">
        <h1 class="title">My Playlist</h1>

        <div class="toolbar" aria-label="Playlist controls">
            <input id="search" type="search" placeholder="Search videos… (title)"
                aria-label="Search videos by title" />
            <button id="searchBtn" aria-label="Search">Search</button>
            <button id="clearBtn" aria-label="Clear search">Clear</button>
        </div>

        <div class="layout">
            <div class="player" id="player" tabindex="0" aria-label="Custom video player">
                <video id="video" crossorigin="anonymous" playsinline preload="metadata" aria-label="Video"></video>
                <div class="scrim" aria-hidden="true"></div>

                <div class="controls" id="controls" role="group" aria-label="Video controls">
                    <div class="progress" id="progress" role="slider" aria-label="Seek" tabindex="0"
                        aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div class="buffered" id="buffered" aria-hidden="true"></div>
                        <div class="played" id="played" aria-hidden="true"></div>
                        <div class="thumb" id="thumb" aria-hidden="true"></div>
                    </div>

                    <div class="row">
                        <div class="left">
                            <button class="btn" id="prev" data-tip="Previous (Shift+P)" aria-label="Previous">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 6h2v12H6zm12 0v12l-8-6z" />
                                </svg>
                            </button>
                            <button class="btn" id="play" data-tip="Play (k/space)" aria-label="Play">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                            <button class="btn" id="next" data-tip="Next (Shift+N)" aria-label="Next">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 6v12l-8-6zM18 6h2v12h-2z" />
                                </svg>
                            </button>
                            <div class="time"><span id="current">0:00</span> / <span id="duration">0:00</span>
                            </div>
                            <div class="vol">
                                <button class="btn" id="mute" data-tip="Mute (m)" aria-label="Mute/Unmute">
                                    <svg id="volIcon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M5 9v6h4l5 4V5L9 9H5z" />
                                    </svg>
                                </button>
                                <input class="range" id="volume" type="range" min="0" max="1"
                                    step="0.01" value="1" aria-label="Volume" />
                            </div>
                        </div>

                        <div class="right">
                            <button class="btn" id="captions" data-tip="Subtitles/CC (c)"
                                aria-label="Toggle captions" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M21 3H3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zM8 15H6a2 2 0 0 1-2-2v-2h2v2h2v2zm12 0h-6a2 2 0 0 1-2-2v-2h2v2h6v2z" />
                                </svg>
                            </button>

                            <div class="menu" id="speedMenu">
                                <button class="btn" data-tip="Playback speed" aria-haspopup="true"
                                    aria-expanded="false" id="speedBtn">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 3a9 9 0 1 0 .001 18.001A9 9 0 0 0 12 3zm1 5h-2v4.59l3.7 3.7 1.42-1.42L13 12.17V8z" />
                                    </svg>
                                </button>
                                <div class="menu-list" role="menu" aria-label="Playback speed">
                                    <div class="menu-item" data-speed="0.5">0.5×</div>
                                    <div class="menu-item" data-speed="0.75">0.75×</div>
                                    <div class="menu-item" data-speed="1">1× (Normal)</div>
                                    <div class="menu-item" data-speed="1.25">1.25×</div>
                                    <div class="menu-item" data-speed="1.5">1.5×</div>
                                    <div class="menu-item" data-speed="1.75">1.75×</div>
                                    <div class="menu-item" data-speed="2">2×</div>
                                </div>
                            </div>

                            <button class="btn" id="pip" data-tip="Picture-in-Picture"
                                aria-label="Picture in Picture">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 7H5v10h14V7zm-2 6h-6V9h6v4z" />
                                </svg>
                            </button>

                            <button class="btn" id="fullscreen" data-tip="Fullscreen (f)"
                                aria-label="Fullscreen">
                                <svg id="fsIcon" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M7 14H5v5h5v-2H7v-3zm0-4h3V7h2V5H5v5h2zm10 9h-3v2h5v-5h-2v3zm0-14h-5v2h3v3h2V5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="playlist">
                <div class="plist-header">
                    <div class="plist-title">Playlist</div>
                    <div class="playing-badge" id="playingBadge" style="display:none" aria-live="polite">Playing
                    </div>
                </div>
                <ul class="plist" id="plist">
                    <li class="empty">Loading videos…</li>
                </ul>
                <div style="padding:10px 12px; display:flex; gap:8px">
                    <button id="loadMore" style="display:none">Load more</button>
                </div>
            </aside>
        </div>

        <p style="opacity:.8;margin-top:12px">
            Shortcuts: <strong>k/space</strong> play/pause, <strong>←/→</strong> ±5s, <strong>↑/↓</strong> volume,
            <strong>m</strong> mute,
            <strong>f</strong> fullscreen, <strong>c</strong> captions, <strong>0-9</strong> seek %,
            <strong>Shift+N</strong> next,
            <strong>Shift+P</strong> previous.
        </p>
    </div>

    <script>
        // ---------- DOM ----------
        const root = document.getElementById('playlistRoot');
        const PLAYLIST_URL = root.dataset.playlistUrl;
        const token = `{{ session()->get('api_token') }}`;

        const player = document.getElementById('player');
        const video = document.getElementById('video');
        const playBtn = document.getElementById('play');
        const nextBtn = document.getElementById('next');
        const prevBtn = document.getElementById('prev');
        const muteBtn = document.getElementById('mute');
        const volIcon = document.getElementById('volIcon');
        const volRange = document.getElementById('volume');
        const progress = document.getElementById('progress');
        const played = document.getElementById('played');
        const bufferedBar = document.getElementById('buffered');
        const thumb = document.getElementById('thumb');
        const currentEl = document.getElementById('current');
        const durationEl = document.getElementById('duration');
        const captionsBtn = document.getElementById('captions');
        const speedMenu = document.getElementById('speedMenu');
        const speedBtn = document.getElementById('speedBtn');
        const pipBtn = document.getElementById('pip');
        const fullscreenBtn = document.getElementById('fullscreen');
        const fsIcon = document.getElementById('fsIcon');
        const plist = document.getElementById('plist');
        const loadMoreBtn = document.getElementById('loadMore');
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('searchBtn');
        const clearBtn = document.getElementById('clearBtn');
        const playingBadge = document.getElementById('playingBadge');

        // ---------- State ----------
        let hideTimer;
        let playlist = [];
        let idx = 0;
        let nextPageUrl = null;
        let currentQuery = '';

        // ---------- Utils ----------
        const fmt = s => !isFinite(s) ? '0:00' : `${Math.floor(s/60)}:${Math.floor(s%60).toString().padStart(2,'0')}`;

        function setControlsVisible(visible = true) {
            player.classList.toggle('controls-visible', visible);
            clearTimeout(hideTimer);
            if (visible) hideTimer = setTimeout(() => player.classList.remove('controls-visible'), 2000);
        }

        function buildUrl(base, params) {
            const url = new URL(base, window.location.origin);
            Object.entries(params || {}).forEach(([k, v]) => {
                if (v !== undefined && v !== null && String(v).length) url.searchParams.set(k, v);
            });
            return url.toString();
        }

        // ---------- Load playlist (array or paginator) ----------
        async function fetchPage(url) {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                cache: 'no-store',
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        }

        async function loadPlaylist({
            reset = true
        } = {}) {
            try {
                const url = buildUrl(PLAYLIST_URL, {
                    q: currentQuery,
                    per_page: 20
                });
                const payload = await fetchPage(url);
                const raw = Array.isArray(payload) ? payload : (payload.data ?? []);
                const items = raw.map(mapVideo);
                playlist = reset ? items : playlist.concat(items);
                nextPageUrl = payload?.links?.next || null;
            } catch (_) {
                playlist = [{
                    id: 'bbb',
                    title: 'Big Buck Bunny (fallback)',
                    src: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    poster: '',
                    tracks: []
                }];
                nextPageUrl = null;
            }
            renderPlaylist();
            loadVideo(0, false);
            updateLoadMoreBtn();
        }

        function updateLoadMoreBtn() {
            loadMoreBtn.style.display = nextPageUrl ? 'inline-block' : 'none';
        }

        function mapVideo(v) {
            return {
                id: v.id,
                title: v.title || `Video ${v.id}`,
                src: v.src,
                poster: v.poster || '',
                tracks: Array.isArray(v.tracks) ? v.tracks : []
            };
        }

        async function loadMore() {
            if (!nextPageUrl) return;
            try {
                const payload = await fetchPage(nextPageUrl);
                const items = (payload?.data ?? []).map(mapVideo);
                const startLen = playlist.length;
                playlist = playlist.concat(items);
                nextPageUrl = payload?.links?.next || null;
                renderPlaylist({
                    preserveScroll: true
                });
                updateActiveItem();
                idx = Math.min(idx, playlist.length - 1);
                updateLoadMoreBtn();
                // Keep scroll roughly where it was
                const ul = plist;
                ul.scrollTop = ul.scrollTop + (playlist.length - startLen) * 78; // approx row height
            } catch (e) {
                console.error(e);
            }
        }

        function renderPlaylist({
            preserveScroll = false
        } = {}) {
            const prevScroll = plist.scrollTop;
            plist.innerHTML = '';
            if (!playlist.length) {
                plist.insertAdjacentHTML('beforeend', '<li class="empty">No videos found.</li>');
                return;
            }
            playlist.forEach((v, i) => {
                const li = document.createElement('li');
                li.className = 'item';
                li.dataset.index = i;
                const safeTitle = (v.title || `Video ${i+1}`).replace(/"/g, '&quot;');
                li.innerHTML = `
          <img class="thumb" alt="${safeTitle}" ${v.poster ? `src="${v.poster}"` : ''} />
          <div class="meta">
            <div class="name">${safeTitle}</div>
            <div class="id">${v.id ? `#${v.id}` : ''}</div>
          </div>
          <div class="playing-badge" style="display:none">Now playing</div>`;
                li.addEventListener('click', () => loadVideo(i, true));
                plist.appendChild(li);
            });
            if (preserveScroll) plist.scrollTop = prevScroll;
            updateActiveItem();
        }

        function updateActiveItem() {
            [...plist.children].forEach((li, i) => {
                li.classList.toggle('active', i === idx);
                const badge = li.querySelector('.playing-badge');
                if (badge) badge.style.display = i === idx ? 'inline-block' : 'none';
            });
            playingBadge.style.display = playlist.length ? 'inline-block' : 'none';
        }

        function clearTracks() {
            video.querySelectorAll('track').forEach(t => t.remove());
        }

        function supportsNativeHls() {
            return video.canPlayType && video.canPlayType('application/vnd.apple.mpegurl');
        }

        async function attachSrc(src) {
            // Basic HLS support without external libs: native Safari and some browsers
            video.src = src || '';
        }

        function loadVideo(newIndex, autoplay) {
            if (!playlist.length) return;
            idx = ((newIndex % playlist.length) + playlist.length) % playlist.length;
            const item = playlist[idx];
            video.pause();
            clearTracks();

            attachSrc(item.src);
            if (item.poster) video.setAttribute('poster', item.poster);
            else video.removeAttribute('poster');

            if (Array.isArray(item.tracks)) {
                item.tracks.forEach(t => {
                    const tr = document.createElement('track');
                    tr.kind = t.kind || 'subtitles';
                    tr.src = t.src;
                    tr.srclang = t.srclang || '';
                    tr.label = t.label || '';
                    video.appendChild(tr);
                });
            }

            video.load();
            if (autoplay) video.play().catch(() => {});
            updateActiveItem();
            updatePlayIcon();
            currentEl.textContent = '0:00';
            durationEl.textContent = '0:00';
            played.style.width = '0%';
            bufferedBar.style.width = '0%';
            thumb.style.left = '0%';
            progress.setAttribute('aria-valuenow', '0');
        }

        function updatePlayIcon() {
            playBtn.innerHTML = video.paused ?
                `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>` :
                `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>`;
            playBtn.setAttribute('aria-label', video.paused ? 'Play' : 'Pause');
            playBtn.setAttribute('data-tip', video.paused ? 'Play (k/space)' : 'Pause (k/space)');
        }
        playBtn.addEventListener('click', () => video.paused ? video.play() : video.pause());
        video.addEventListener('play', updatePlayIcon);
        video.addEventListener('pause', updatePlayIcon);

        video.addEventListener('loadedmetadata', () => {
            durationEl.textContent = fmt(video.duration);
            updateBuffered();
        });
        video.addEventListener('timeupdate', () => {
            currentEl.textContent = fmt(video.currentTime);
            const pct = (video.currentTime / video.duration) * 100 || 0;
            played.style.width = pct + '%';
            thumb.style.left = pct + '%';
            progress.setAttribute('aria-valuenow', String(Math.round(pct)));
        });

        function updateBuffered() {
            try {
                const {
                    buffered,
                    duration,
                    currentTime
                } = video;
                if (!buffered || buffered.length === 0) return;
                // Find the range containing currentTime, else take the last
                let end = buffered.end(buffered.length - 1);
                for (let i = 0; i < buffered.length; i++) {
                    if (currentTime >= buffered.start(i) && currentTime <= buffered.end(i)) {
                        end = buffered.end(i);
                        break;
                    }
                }
                const pct = (end / duration) * 100 || 0;
                bufferedBar.style.width = pct + '%';
            } catch (_) {}
        }
        video.addEventListener('progress', updateBuffered);

        function pctFromEvent(e) {
            const rect = progress.getBoundingClientRect();
            const x = (e.clientX ?? (e.touches?.[0]?.clientX || 0)) - rect.left;
            return Math.min(1, Math.max(0, x / rect.width));
        }

        function seekToPct(p) {
            video.currentTime = (video.duration || 0) * p;
        }

        // Pointer seek
        progress.addEventListener('pointerdown', e => {
            e.preventDefault();
            const move = ev => seekToPct(pctFromEvent(ev));
            move(e);
            window.addEventListener('pointermove', move);
            window.addEventListener('pointerup', () => window.removeEventListener('pointermove', move), {
                once: true
            });
        });

        // Keyboard seek for slider accessibility
        progress.addEventListener('keydown', (e) => {
            const dur = video.duration || 0;
            if (!dur) return;
            const step = 5; // seconds
            let handled = true;
            switch (e.key) {
                case 'ArrowLeft':
                    video.currentTime = Math.max(0, video.currentTime - step);
                    break;
                case 'ArrowRight':
                    video.currentTime = Math.min(dur, video.currentTime + step);
                    break;
                case 'Home':
                    video.currentTime = 0;
                    break;
                case 'End':
                    video.currentTime = dur;
                    break;
                case 'PageUp':
                    video.currentTime = Math.min(dur, video.currentTime + 10);
                    break;
                case 'PageDown':
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;
                default:
                    handled = false;
            }
            if (handled) {
                e.preventDefault();
                setControlsVisible(true);
            }
        });

        function updateVolIcon() {
            const v = video.muted ? 0 : video.volume;
            let path;
            if (v === 0) {
                // Muted icon
                path = 'M5 9v6h4l5 4V5L9 9H5z M16 8l5 5-1.4 1.4L14.6 9.4 16 8z';
            } else if (v < .5) {
                // Low volume
                path = 'M5 9v6h4l5 4V5L9 9H5zm4.5-2.5l1.5-1.5a7 7 0 010 10l-1.5-1.5a5 5 0 000-7z';
            } else {
                // High volume
                path = 'M5 9v6h4l5 4V5L9 9H5zm3.5-2.5l1.5-1.5a9 9 0 010 13l-1.5-1.5a7 7 0 000-10z';
            }
            volIcon.innerHTML = `<path d="${path}"/>`;
        }
        volRange.addEventListener('input', e => {
            const v = parseFloat(e.target.value);
            video.volume = v;
            video.muted = v === 0;
            e.target.style.setProperty('--val', (v * 100) + '%');
            updateVolIcon();
        });
        muteBtn.addEventListener('click', () => {
            video.muted = !video.muted;
            if (!video.muted && video.volume === 0) {
                video.volume = 0.3;
                volRange.value = 0.3;
            }
            updateVolIcon();
        });
        video.addEventListener('volumechange', updateVolIcon);

        function hasTrack() {
            return video.textTracks && video.textTracks.length > 0;
        }

        function toggleCaptions() {
            if (!hasTrack()) return;
            const tt = video.textTracks[0];
            tt.mode = (tt.mode === 'showing') ? 'hidden' : 'showing';
            captionsBtn.classList.toggle('active', tt.mode === 'showing');
            captionsBtn.setAttribute('aria-pressed', String(tt.mode === 'showing'));
        }
        captionsBtn.addEventListener('click', toggleCaptions);

        speedBtn.addEventListener('click', () => {
            const open = speedMenu.classList.toggle('open');
            speedBtn.setAttribute('aria-expanded', String(open));
        });
        speedMenu.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                video.playbackRate = parseFloat(item.dataset.speed);
                speedMenu.classList.remove('open');
            });
        });
        document.addEventListener('click', e => {
            if (!speedMenu.contains(e.target)) speedMenu.classList.remove('open');
        });

        pipBtn.addEventListener('click', async () => {
            try {
                if (document.pictureInPictureElement) await document.exitPictureInPicture();
                else if (document.pictureInPictureEnabled && !video.disablePictureInPicture) await video
                    .requestPictureInPicture();
            } catch (_) {}
        });

        function inFullscreen() {
            return document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
        }
        async function toggleFullscreen() {
            try {
                if (!inFullscreen()) await (player.requestFullscreen?.() || player.webkitRequestFullscreen?.() || player
                    .msRequestFullscreen?.());
                else await (document.exitFullscreen?.() || document.webkitExitFullscreen?.() || document
                    .msExitFullscreen?.());
            } catch (_) {}
        }
        fullscreenBtn.addEventListener('click', toggleFullscreen);
        document.addEventListener('fullscreenchange', updateFsIcon);
        document.addEventListener('webkitfullscreenchange', updateFsIcon);

        function updateFsIcon() {
            fsIcon.innerHTML = inFullscreen() ?
                `<path d="M7 10H5V5h5v2H7v3zm0 4h3v3h2v3H5v-5h2v-1zm10-9h-3V3h5v5h-2V5zm0 9h2v5h-5v-2h3v-3z"/>` :
                `<path d="M7 14H5v5h5v-2H7v-3zm0-4h3V7h2V5H5v5h2zm10 9h-3v2h5v-5h-2v3zm0-14h-5v2h3v3h2V5z"/>`;
        }

        function next() {
            loadVideo(idx + 1, true);
        }

        function prev() {
            loadVideo(idx - 1, true);
        }
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);
        video.addEventListener('ended', next);

        player.addEventListener('keydown', (e) => {
            const tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input') return;
            switch (e.key.toLowerCase()) {
                case ' ':
                case 'k':
                    e.preventDefault();
                    video.paused ? video.play() : video.pause();
                    break;
                case 'arrowleft':
                    video.currentTime = Math.max(0, video.currentTime - 5);
                    break;
                case 'arrowright':
                    video.currentTime = Math.min(video.duration, video.currentTime + 5);
                    break;
                case 'arrowup':
                    video.volume = Math.min(1, (video.volume + .05));
                    volRange.value = video.volume;
                    volRange.dispatchEvent(new Event('input'));
                    break;
                case 'arrowdown':
                    video.volume = Math.max(0, (video.volume - .05));
                    volRange.value = video.volume;
                    volRange.dispatchEvent(new Event('input'));
                    break;
                case 'm':
                    video.muted = !video.muted;
                    updateVolIcon();
                    break;
                case 'f':
                    toggleFullscreen();
                    break;
                case 'c':
                    toggleCaptions();
                    break;
                case 'n':
                    if (e.shiftKey) next();
                    break;
                case 'p':
                    if (e.shiftKey) prev();
                    break;
                default:
                    if (/^[0-9]$/.test(e.key)) {
                        const pct = parseInt(e.key, 10) / 10;
                        video.currentTime = (video.duration || 0) * pct;
                    }
            }
        });

        ['mousemove', 'pointerdown', 'touchstart', 'touchmove'].forEach(evt => player.addEventListener(evt, () =>
            setControlsVisible(true)));
        player.addEventListener('mouseleave', () => player.classList.remove('controls-visible'));

        // Search & pagination
        function doSearch() {
            currentQuery = searchInput.value.trim();
            loadPlaylist({
                reset: true
            });
        }
        searchBtn.addEventListener('click', doSearch);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            if (currentQuery) {
                currentQuery = '';
                loadPlaylist({
                    reset: true
                });
            }
        });
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') doSearch();
        });
        loadMoreBtn.addEventListener('click', loadMore);

        // Init
        (async () => {
            await loadPlaylist();
            updateVolIcon();
            updateFsIcon();
            setControlsVisible(true);
        })();
    </script>
</body>

</html>
