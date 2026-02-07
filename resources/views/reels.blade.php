@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-black z-50 flex flex-col md:flex-row">
    <!-- Back Button -->
    <a href="{{ route('home') }}" class="absolute top-4 left-4 z-50 p-2 bg-black/50 rounded-full text-white hover:bg-black/80 transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>

    <!-- Main Reels Container (Snap Scroll) -->
    <div class="flex-1 h-full overflow-y-scroll snap-y snap-mandatory scrollbar-hide relative" id="reelsContainer">
        <!-- Loader -->
        <div id="initialLoader" class="absolute inset-0 flex items-center justify-center text-white">
            <div class="w-10 h-10 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
        </div>
    </div>
</div>

<script>
    let reels = [];
    let players = {};
    let observer;
    let nextCursor = null;
    let isLoading = false;
    let currentReelId = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadReels();
    });

    async function loadReels() {
        if (isLoading) return;
        isLoading = true;
        
        try {
            let newPosts = [];
            const urlParams = new URLSearchParams(window.location.search);
            const startId = urlParams.get('start');

            // 1. If startId exists and this is the first load, fetch that post specifically
            if (startId && reels.length === 0) {
                try {
                    const specificRes = await window.bridge.request(`/posts/${startId}`);
                    if (specificRes.data) {
                        newPosts.push(specificRes.data);
                    }
                } catch (e) {
                    console.error('Failed to load start post', e);
                }
            }

            // 2. Fetch Discovery Feed
            const res = await window.bridge.request(`/posts/discovery?type=video&cursor=${nextCursor || ''}`);
            const discoveryPosts = res.data.filter(p => p.media.some(m => m.type === 'video'));
            
            // 3. Merge and Deduplicate
            // If we fetched a start post, it might also be in discovery, so filter it out from discovery to avoid duplicates
            const filteredDiscovery = discoveryPosts.filter(d => !newPosts.find(n => n.id === d.id));
            newPosts = [...newPosts, ...filteredDiscovery];
            
            // Global Dedup against existing reels
            newPosts = newPosts.filter(p => !reels.find(r => r.id === p.id));
            
            reels = [...reels, ...newPosts];
            nextCursor = res.next_cursor;
            
            document.getElementById('initialLoader').classList.add('hidden');
            renderReels(newPosts);
            
            // 4. Scroll to start post if applicable
            if (startId && newPosts.some(p => p.id == startId)) {
                setTimeout(() => {
                    const el = document.querySelector(`[data-id="${startId}"]`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'auto' }); // Instant jump
                    }
                }, 100);
            }
            
        } catch (err) {
            console.error('Failed to load reels', err);
        } finally {
            isLoading = false;
        }
    }

    function renderReels(posts) {
        const container = document.getElementById('reelsContainer');
        
        posts.forEach(post => {
            const media = post.media.find(m => m.type === 'video');
            if (!media) return;

            const el = document.createElement('div');
            el.className = 'w-full h-full snap-start relative bg-black flex items-center justify-center';
            el.dataset.id = post.id;
            el.innerHTML = `
                <div class="w-full h-full md:w-[450px] relative bg-gray-900 overflow-hidden shadow-2xl video-container" id="video-${post.id}">
                    <!-- Video via JS -->
                </div>
            `;
            container.appendChild(el);
            
            // Init intersection observer
            if (!observer) initObserver();
            observer.observe(el);
        });
    }

    let viewTimers = {};

    function initObserver() {
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const id = entry.target.dataset.id;
                const container = entry.target.querySelector('.video-container');
                const post = reels.find(r => r.id == id);
                
                if (entry.isIntersecting) {
                    currentReelId = id;
                    // Initialize player if not exists
                    if (!players[id] && post) {
                        const media = post.media.find(m => m.type === 'video');
                        players[id] = new VideoPlayer(container, media.url, {
                            autoplay: true,
                            loop: true,
                            muted: true,
                            isReel: true,
                            hideControls: false,
                            poster: media.thumbnail_url,
                            postId: post.id,
                            likesCount: post.likes_count,
                            commentsCount: post.comments_count,
                            isLiked: post.is_liked,
                            caption: post.caption,
                            user: post.user,
                            fullPost: post
                        });

                        // Attach Event Listeners for View Tracking (3s threshold)
                        const v = players[id].video;
                        v.addEventListener('play', () => {
                             if (viewTimers[id]) clearTimeout(viewTimers[id]);
                             viewTimers[id] = setTimeout(() => {
                                 window.bridge.request(`/posts/${id}/view`, { method: 'POST' }).catch(() => {});
                             }, 3000);
                        });
                        v.addEventListener('pause', () => { if (viewTimers[id]) clearTimeout(viewTimers[id]); });
                        v.addEventListener('ended', () => { if (viewTimers[id]) clearTimeout(viewTimers[id]); });
                        v.addEventListener('waiting', () => { if (viewTimers[id]) clearTimeout(viewTimers[id]); });

                    } else {
                        players[id]?.play();
                    }
                    
                } else {
                    // Pause/Dispose when out of view
                    if (players[id]) {
                        players[id].pause();
                    }
                }
            });
        }, { threshold: 0.6 });
    }

    // TODO: Reuse existing functions for Like/Comment if possible, or reimplement lightweight
    async function toggleLike(id, btn) {
        const post = reels.find(r => r.id == id);
        if (!post) return;
        
        // Optimistic UI
        post.is_liked = !post.is_liked;
        post.likes_count += post.is_liked ? 1 : -1;
        
        // Update UI locally
        const likeBtn = btn || document.querySelector(`[data-id="${id}"] .like-btn`);
        if (likeBtn) {
            const svg = likeBtn.querySelector('svg');
            const count = likeBtn.nextElementSibling && likeBtn.nextElementSibling.classList.contains('likes-count') 
                ? likeBtn.nextElementSibling 
                : likeBtn.querySelector('.likes-count');

            if (post.is_liked) {
                svg.classList.add('text-accent-500', 'fill-accent-500');
                if (svg.getAttribute('fill') !== 'currentColor') svg.setAttribute('fill', 'currentColor');
            } else {
                svg.classList.remove('text-accent-500', 'fill-accent-500');
                svg.setAttribute('fill', 'none');
            }
            if (count) count.innerText = post.likes_count;
        }

        try {
            await window.bridge.request(`/posts/${id}/like`, { method: 'POST' });
        } catch (e) {
            console.error(e);
            // Revert on error
        }
    }

    function openComments(id) {
        if (typeof openCommentsSheet === 'function') {
            openCommentsSheet(id, 'reels');
        } else {
            window.location.href = `/explore?post_id=${id}`;
        }
    }

    async function toggleFollow(userId, btn) {
        await toggleFollowUniversal(userId, btn);
    }

    function shareReel(postId) {
        const post = reels.find(r => r.id == postId);
        if (!post) return;
        
        const url = `${window.location.origin}/reels?start=${postId}`;
        
        // Use Web Share API if available (mobile)
        if (navigator.share) {
            navigator.share({
                title: `Reel by @${post.user.username}`,
                text: post.caption || 'Check out this reel!',
                url: url
            }).then(() => {
                window.toast?.('Shared!', 'success');
            }).catch(err => {
                console.log('Share cancelled', err);
            });
        } else {
            // Robust clipboard fallback
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    window.toast?.('Link copied to clipboard!', 'success');
                }).catch(() => {
                    fallbackCopyTextToClipboard(url);
                });
            } else {
                fallbackCopyTextToClipboard(url);
            }
        }
    }

    function fallbackCopyTextToClipboard(text) {
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
            window.toast?.('Link copied to clipboard!', 'success');
        } catch (err) {
            window.toast?.('Failed to copy link', 'error');
        }
        document.body.removeChild(textArea);
    }
</script>
@endsection
