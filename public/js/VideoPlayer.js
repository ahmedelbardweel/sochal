class VideoPlayer {
    constructor(container, src, options = {}) {
        this.container = container;
        this.src = src;
        this.currentQuality = 'original';
        this.options = {
            autoplay: false,
            muted: false,
            loop: false,
            isReel: false,
            poster: null,
            postId: null,
            likesCount: 0,
            commentsCount: 0,
            isLiked: false,
            caption: '',
            user: null,
            fullPost: null,
            hideControls: false,
            hideSidebar: false,
            hideCaption: false,
            ...options
        };

        this.hls = null;
        this.isLoaded = false;
        this.isPrepared = false;
        this.savedTime = 0;
        this.lastTap = 0;
        this.init();
    }

    init() {
        this.container.innerHTML = '';
        this.container.classList.add('relative', 'overflow-hidden', 'bg-black', 'w-full', 'h-full', 'flex', 'items-center', 'justify-center', 'group', 'select-none');

        // Video Element
        this.video = document.createElement('video');
        this.video.className = 'w-full h-full object-contain relative z-10';
        this.video.playsInline = true;
        this.video.loop = this.options.loop;
        this.video.muted = this.options.muted;
        this.video.preload = 'none';
        if (this.options.poster) this.video.poster = this.options.poster;

        // Interactive Layer
        this.clickLayer = document.createElement('div');
        this.clickLayer.className = 'absolute inset-0 z-20 cursor-pointer';

        // --- PREMIUM OVERLAYS ---

        // 1. Vertical Sidebar (Like, Comment, Share)
        this.sidebar = document.createElement('div');
        this.sidebar.className = `absolute right-4 bottom-32 z-40 flex flex-col items-center gap-6 pointer-events-auto opacity-0 translate-x-4 transition-all duration-500 group-hover:opacity-100 group-hover:translate-x-0 ${this.options.hideSidebar ? 'hidden' : ''}`;
        this.sidebar.innerHTML = `
            <div class="flex flex-col items-center gap-1 group/btn">
                <button class="like-btn w-12 h-12 rounded-full bg-black/40 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white transition-all active:scale-90 ${this.options.isLiked ? 'text-accent-500' : 'hover:bg-white/10'}">
                    <svg class="w-6 h-6 ${this.options.isLiked ? 'fill-accent-500' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                <span class="likes-count text-[11px] font-black text-white/90 drop-shadow-lg">${this.options.likesCount}</span>
            </div>
            <div class="flex flex-col items-center gap-1 group/btn">
                <button class="comment-btn w-12 h-12 rounded-full bg-black/40 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white transition-all hover:bg-white/10 active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </button>
                <span class="text-[11px] font-black text-white/90 drop-shadow-lg">${this.options.commentsCount}</span>
            </div>
            <div class="flex flex-col items-center gap-1 group/btn">
                <button class="share-btn w-12 h-12 rounded-full bg-black/40 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white transition-all hover:bg-white/10 active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg>
                </button>
            </div>
        `;

        // 2. Caption/User Overlay (Bottom Left)
        const followState = this.options.user ? getFollowButtonState(this.options.user) : null;
        const isSelf = window.currentUser && this.options.user && window.currentUser.id === this.options.user.id;

        this.captionOverlay = document.createElement('div');
        this.captionOverlay.className = `absolute bottom-28 left-6 right-20 z-40 pointer-events-none opacity-0 translate-y-4 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 ${this.options.hideCaption ? 'hidden' : ''}`;
        this.captionOverlay.innerHTML = `
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="cursor-pointer pointer-events-auto active:scale-95 transition-transform" onclick="window.location.href='/profile/${this.options.user?.username}'">
                        <img src="${this.options.user?.avatar_url || 'https://ui-avatars.com/api/?name=' + this.options.user?.username}" class="w-10 h-10 rounded-full border border-white/20 shadow-lg object-cover">
                    </div>
                    <div class="flex flex-col min-w-0 pointer-events-auto cursor-pointer" onclick="window.location.href='/profile/${this.options.user?.username}'">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black text-white italic drop-shadow-md truncate">${this.options.user?.display_name || this.options.user?.username || 'NeuralUser'}</p>
                            ${(followState && !isSelf) ? `
                                <button onclick="event.stopPropagation(); toggleFollowUniversal(${this.options.user.id}, this)" 
                                        class="font-bold text-[11px] hover:text-primary-400 transition-colors ml-1 ${followState.linkClasses}">
                                    ${followState.text}
                                </button>
                            ` : ''}
                        </div>
                        <p class="text-[10px] text-white/60 font-medium truncate">@${this.options.user?.username || 'neural'}</p>
                    </div>
                </div>
                <p class="text-xs text-white/90 leading-relaxed font-medium line-clamp-2 drop-shadow-sm max-w-sm pointer-events-auto cursor-pointer">${this.options.caption || ''}</p>
            </div>
        `;

        // Center Anim (Play/Pause/Ripple)
        this.centerIcon = document.createElement('div');
        this.centerIcon.className = 'absolute inset-0 flex items-center justify-center pointer-events-none z-30';
        this.centerIcon.innerHTML = `
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-xl border border-white/30 transform scale-0 transition-transform duration-300 centerIconMain">
                <svg class="w-12 h-12 text-white centerIconSvg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <div class="absolute w-20 h-20 bg-white/30 rounded-full border-2 border-white/50 opacity-0 scale-50 transition-all duration-500 pointer-events-none seekRipple"></div>
        `;

        // 3. Control Center
        this.controls = document.createElement('div');
        this.controls.className = `
            absolute bottom-6 left-6 right-6 p-4 
            bg-black/40 backdrop-blur-2xl rounded-[2rem] border border-white/10 
            pointer-events-auto flex flex-col gap-3 z-50 
            opacity-0 translate-y-6 transition-all duration-500 ease-out 
            group-hover:opacity-100 group-hover:translate-y-0 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7)]
            ${this.options.hideControls ? 'hidden' : ''}
        `;

        this.seekContainer = document.createElement('div');
        this.seekContainer.className = 'relative h-1 w-full bg-white/10 rounded-full cursor-pointer group/seek transition-all hover:h-2';
        this.seekContainer.onclick = (e) => this.seek(e);

        this.progressBar = document.createElement('div');
        this.progressBar.className = 'absolute top-0 left-0 h-full bg-gradient-to-r from-primary-500 to-primary-300 rounded-full w-0 transition-all duration-100 ease-linear pointer-events-none shadow-[0_0_15px_rgba(59,130,246,0.5)]';

        this.seekHandle = document.createElement('div');
        this.seekHandle.className = 'absolute top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-xl transform scale-0 group-hover/seek:scale-100 transition-transform duration-200 border-2 border-primary-500';
        this.progressBar.appendChild(this.seekHandle);
        this.seekContainer.appendChild(this.progressBar);

        const toolbar = document.createElement('div');
        toolbar.className = 'flex items-center justify-between px-2';

        const mainTools = document.createElement('div');
        mainTools.className = 'flex items-center gap-5';

        const backBtn = document.createElement('button');
        backBtn.className = 'text-white/60 hover:text-white transition-colors active:scale-90';
        backBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>';
        backBtn.onclick = () => this.video.currentTime -= 10;

        this.playBtn = document.createElement('button');
        this.playBtn.className = 'text-white w-8 h-8 flex items-center justify-center hover:scale-110 transition-transform';
        this.playBtn.innerHTML = '<svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
        this.playBtn.onclick = () => this.togglePlay();

        const forwardBtn = document.createElement('button');
        forwardBtn.className = 'text-white/60 hover:text-white transition-colors active:scale-90';
        forwardBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>';
        forwardBtn.onclick = () => this.video.currentTime += 10;

        this.volumeContainer = document.createElement('div');
        this.volumeContainer.className = 'flex items-center gap-2 group/vol';
        this.muteBtn = document.createElement('button');
        this.muteBtn.className = 'text-white/80 hover:text-white transition-opacity';
        this.muteBtn.onclick = (e) => { e.stopPropagation(); this.toggleMute(); };

        this.volumeSlider = document.createElement('input');
        this.volumeSlider.type = 'range'; this.volumeSlider.min = 0; this.volumeSlider.max = 1; this.volumeSlider.step = 0.01;
        this.volumeSlider.value = this.options.muted ? 0 : 1;
        this.volumeSlider.className = 'w-0 group-hover/vol:w-20 transition-all duration-300 opacity-0 group-hover/vol:opacity-100 h-1 accent-primary-500 cursor-pointer';
        this.volumeSlider.oninput = (e) => this.setVolume(e.target.value);
        this.volumeContainer.append(this.muteBtn, this.volumeSlider);

        mainTools.append(backBtn, this.playBtn, forwardBtn, this.volumeContainer);

        const metaTools = document.createElement('div');
        metaTools.className = 'flex items-center gap-4';

        this.timeLabel = document.createElement('div');
        this.timeLabel.className = 'text-[10px] text-white/60 font-black tracking-widest tabular-nums';
        this.timeLabel.innerHTML = '<span class="current">0:00</span> <span class="mx-1">/</span> <span class="duration">0:00</span>';

        this.qualityBtn = document.createElement('button');
        this.qualityBtn.className = 'p-1.5 hover:bg-white/10 rounded-xl transition-colors text-white/80';
        this.qualityBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        this.qualityBtn.onclick = (e) => this.toggleMenu(this.qualityMenu, e);

        this.fsBtn = document.createElement('button');
        this.fsBtn.className = 'p-1.5 hover:bg-white/10 rounded-xl transition-colors text-white/80';
        this.fsBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>';
        this.fsBtn.onclick = () => this.toggleFullscreen();

        metaTools.append(this.timeLabel, this.qualityBtn, this.fsBtn);
        toolbar.append(mainTools, metaTools);

        this.controls.append(this.seekContainer, toolbar);

        this.qualityMenu = document.createElement('div');
        this.qualityMenu.className = 'absolute bottom-28 right-6 bg-black/60 backdrop-blur-[50px] border border-white/10 rounded-[1.5rem] p-2 hidden z-[60] min-w-[160px] shadow-2xl';
        this.renderQualityMenu();

        this.container.append(this.video, this.clickLayer, this.sidebar, this.captionOverlay, this.centerIcon, this.controls, this.qualityMenu);

        this.spinner = document.createElement('div');
        this.spinner.className = 'absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-[75] bg-black/40 backdrop-blur-xl opacity-0 transition-opacity duration-500';
        this.spinner.innerHTML = `<div class="w-10 h-10 border-4 border-primary-500/20 border-t-primary-500 rounded-full animate-spin"></div><div class="mt-4 text-[9px] font-black text-white/60 uppercase tracking-[0.3em] spinnerLabel">Loading</div>`;
        this.container.append(this.spinner);

        this.updateMuteIcon();
        this.bindEvents();

        if (this.options.autoplay) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => entry.isIntersecting ? this.play() : this.pause());
            }, { threshold: 0.6 });
            this.observer.observe(this.container);
        }

        // --- Event Handlers ---
        this.clickLayer.onclick = (e) => {
            const now = Date.now();
            if (now - this.lastTap < 300) {
                // Double Tap (Like)
                this.animateRipple();
                if (window.toggleLike) window.toggleLike(this.options.postId, this.container.querySelector('.like-btn'));
            } else {
                // Single Tap (Play/Pause)
                this.togglePlay();
            }
            this.lastTap = now;
        };

        this.container.querySelector('.like-btn').onclick = (e) => {
            e.stopPropagation();
            if (window.toggleLike) window.toggleLike(this.options.postId, e.currentTarget);
        };
        this.container.querySelector('.comment-btn').onclick = (e) => {
            e.stopPropagation();
            if (window.openCommentsSheet) {
                window.openCommentsSheet(this.options.postId, 'feed');
            }
        };
        this.container.querySelector('.share-btn').onclick = (e) => {
            e.stopPropagation();
            this.sharePost();
        };
    }

    animateRipple() {
        const ripple = this.container.querySelector('.seekRipple');
        ripple.classList.remove('opacity-0', 'scale-50');
        ripple.classList.add('opacity-100', 'scale-150');
        setTimeout(() => {
            ripple.classList.add('opacity-0', 'scale-[200]');
            setTimeout(() => ripple.classList.remove('scale-[200]', 'scale-150'), 500);
        }, 200);
    }

    sharePost() {
        const url = `${window.location.origin}/post/${this.options.postId}`;
        if (navigator.share) {
            navigator.share({ title: 'AbsScroll Post', text: this.options.caption, url: url }).catch(() => { });
        } else {
            this.copyToClipboard(url);
        }
    }

    copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                window.toast?.('Link copied to clipboard', 'success');
            }).catch(() => {
                this.fallbackCopyTextToClipboard(text);
            });
        } else {
            this.fallbackCopyTextToClipboard(text);
        }
    }

    fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            window.toast?.('Link copied to clipboard', 'success');
        } catch (err) {
            window.toast?.('Failed to copy link', 'error');
        }
        document.body.removeChild(textArea);
    }

    prepareSource() {
        if (this.isPrepared) return;
        this.isPrepared = true;
        const isHLS = typeof this.src === 'string' && this.src.includes('.m3u8');
        if (isHLS && window.Hls) {
            if (window.Hls.isSupported()) {
                this.hls = new window.Hls();
                this.hls.loadSource(this.src);
                this.hls.attachMedia(this.video);
            } else if (this.video.canPlayType('application/vnd.apple.mpegurl')) {
                this.video.src = this.src;
            }
        } else {
            let initialSrc = typeof this.src === 'string' ? this.src : (this.src[this.currentQuality] || Object.values(this.src)[0]);
            this.video.src = initialSrc;
            if (this.savedTime > 0) {
                const onMetadata = () => {
                    this.video.currentTime = this.savedTime;
                    this.video.removeEventListener('loadedmetadata', onMetadata);
                };
                this.video.addEventListener('loadedmetadata', onMetadata);
            }
            this.video.load();
        }
    }

    bindEvents() {
        this.video.onwaiting = () => this.showSpinner('Buffering...');
        this.video.onseeking = () => this.showSpinner('Seeking...');
        const hideLoading = () => this.spinner.classList.add('opacity-0');
        this.video.onplaying = () => { hideLoading(); this.container.style.backgroundImage = 'none'; };
        this.video.oncanplay = hideLoading;
        this.video.onseeked = hideLoading;
        this.video.onloadeddata = hideLoading;
        this.video.onerror = () => {
            if (this.currentQuality !== 'original' && typeof this.src === 'object') {
                this.switchQuality('original');
            } else {
                hideLoading();
                this.spinner.classList.remove('opacity-0');
                this.spinner.querySelector('.spinnerLabel').innerText = 'Error Loading Video';
            }
        };
        this.video.onloadedmetadata = () => {
            this.timeLabel.querySelector('.duration').innerText = this.formatTime(this.video.duration);
        };
        this.video.ontimeupdate = () => {
            if (!this.video.duration) return;
            const p = (this.video.currentTime / this.video.duration) * 100;
            this.progressBar.style.width = `${p}%`;
            this.seekHandle.style.left = `calc(${p}% - 8px)`;
            this.timeLabel.querySelector('.current').innerText = this.formatTime(this.video.currentTime);
        };
        window.addEventListener('keydown', (e) => {
            if (!this.isInView()) return;
            switch (e.code) {
                case 'Space': e.preventDefault(); this.togglePlay(); break;
                case 'KeyF': this.toggleFullscreen(); break;
                case 'KeyM': this.toggleMute(); break;
                case 'ArrowRight': this.video.currentTime += 5; break;
                case 'ArrowLeft': this.video.currentTime -= 5; break;
            }
        });
    }

    play() {
        if (!this.isPrepared) this.prepareSource();
        if (this.video.paused) {
            this.video.play().catch(() => {
                this.video.muted = true;
                this.video.play().catch(() => { });
            });
            this.playBtn.innerHTML = '<svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
        }
    }

    pause() {
        this.video.pause();
        this.playBtn.innerHTML = '<svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
    }

    stop() {
        if (!this.isPrepared) return;
        this.savedTime = this.video.currentTime;
        this.video.pause();
        if (this.options.poster) this.container.style.backgroundImage = `url(${this.options.poster})`;
        this.video.src = '';
        this.video.load();
        this.isPrepared = false;
        if (this.hls) { this.hls.destroy(); this.hls = null; }
    }

    togglePlay() {
        if (this.video.paused) { this.play(); this.animateCenterIcon('play'); }
        else { this.pause(); this.animateCenterIcon('pause'); }
    }

    animateCenterIcon(state) {
        const div = this.container.querySelector('.centerIconMain');
        const svg = this.container.querySelector('.centerIconSvg');
        svg.innerHTML = state === 'play' ? '<path d="M8 5v14l11-7z"/>' : '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
        div.classList.remove('scale-0');
        setTimeout(() => div.classList.add('scale-0'), 400);
    }

    toggleMute() {
        this.video.muted = !this.video.muted;
        this.volumeSlider.value = this.video.muted ? 0 : this.video.volume;
        this.updateMuteIcon();
    }

    setVolume(val) {
        this.video.volume = val;
        this.video.muted = val === 0;
        this.updateMuteIcon();
    }

    updateMuteIcon() {
        const vol = this.video.muted ? 0 : this.video.volume;
        let icon = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>';
        if (vol === 0) icon = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.58.45-1.24.8-1.97 1.01v2.06c1.28-.3 2.4-.92 3.32-1.76L19.73 21 21 19.73 4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>';
        this.muteBtn.innerHTML = icon;
    }

    toggleFullscreen() {
        const el = this.container;
        const video = this.video;

        // iOS Safari Fullscreen (Video only support)
        if (video.webkitEnterFullscreen) {
            video.webkitEnterFullscreen();
            return;
        }

        // Standard Fullscreen API with prefixes
        const requestFS = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
        const exitFS = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
        const fsElement = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;

        if (!fsElement) {
            if (requestFS) {
                requestFS.call(el).then(() => {
                    // Try to rotate to landscape if screen orientation API is available
                    if (window.screen && window.screen.orientation && window.screen.orientation.lock) {
                        window.screen.orientation.lock('landscape').catch(() => {
                            // Ignore if locked/not supported
                        });
                    }
                }).catch(() => {
                    // Fallback for mobile devices that only allow video FS
                    if (video.requestFullscreen) video.requestFullscreen();
                });
            }
        } else {
            if (exitFS) exitFS.call(document);
        }
    }

    showSpinner(text) {
        const label = this.spinner.querySelector('.spinnerLabel');
        if (label) label.innerText = text;
        this.spinner.classList.remove('opacity-0');
    }

    seek(e) {
        if (!this.video.duration) return;
        const rect = this.seekContainer.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        this.video.currentTime = pos * this.video.duration;
    }

    renderQualityMenu() {
        const qualities = this.options.qualities || ['1080p', '720p', '480p', '360p', '140p', 'Auto'];
        this.qualityMenu.innerHTML = '<p class="text-[10px] text-white/40 font-black px-4 py-3 uppercase tracking-[0.2em]">Resolution</p>';
        qualities.forEach(q => {
            const btn = document.createElement('button');
            btn.className = `w-full text-left px-4 py-2.5 text-xs font-bold transition-all ${this.currentQuality === q ? 'text-primary-400 bg-white/5' : 'text-white/80 hover:bg-white/10'}`;
            btn.innerText = q;
            btn.onclick = (e) => { e.stopPropagation(); this.switchQuality(q); };
            this.qualityMenu.appendChild(btn);
        });
    }

    switchQuality(q) {
        if (this.currentQuality === q) return;
        const oldTime = this.video.currentTime;
        const wasPaused = this.video.paused;
        this.currentQuality = q;
        this.renderQualityMenu();
        this.qualityMenu.classList.add('hidden');
        if (typeof this.src === 'object' && this.src[q]) {
            this.showSpinner(`Switching to ${q}...`);
            this.video.src = this.src[q];
            this.video.currentTime = oldTime;
            if (!wasPaused) this.video.play().catch(() => { });
        }
    }

    toggleMenu(menu, e) { e.stopPropagation(); menu.classList.toggle('hidden'); }

    formatTime(sec) {
        if (isNaN(sec)) return '0:00';
        const h = Math.floor(sec / 3600), m = Math.floor((sec % 3600) / 60), s = Math.floor(sec % 60);
        return `${h > 0 ? h + ':' : ''}${m}:${s < 10 ? '0' + s : s}`;
    }

    isInView() {
        const r = this.container.getBoundingClientRect();
        return r.top >= -500 && r.bottom <= window.innerHeight + 500;
    }

    dispose() {
        if (this.hls) this.hls.destroy();
        this.video.pause();
        this.video.src = '';
        this.video.load();
        this.container.innerHTML = '';
    }
}

window.VideoPlayer = VideoPlayer;
