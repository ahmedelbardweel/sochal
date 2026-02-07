@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary">
    @include('partials.sidebar')

    <main class="flex-1 max-w-4xl mx-auto pb-20 md:pb-0" id="profilePage">
        <!-- Profile Header Loading State -->
        <div id="profileHeaderLoading" class="animate-pulse">
            <div class="h-48 md:h-64 bg-bg-secondary"></div>
            <div class="px-6 -mt-12 md:-mt-16 flex flex-col md:flex-row md:items-end justify-between">
                <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-6">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-bg-tertiary border-4 border-bg-primary shadow-xl"></div>
                    <div class="pb-2 space-y-2"><div class="h-6 bg-bg-tertiary rounded w-48"></div><div class="h-4 bg-bg-tertiary rounded w-24"></div></div>
                </div>
            </div>
        </div>

        <!-- Dynamic Profile Content -->
        <div id="profileContent" class="hidden">
            <!-- Cover & Avatar -->
            <div class="relative group">
                <div class="h-48 md:h-64 bg-primary-900 overflow-hidden">
                    <img id="coverImg" src="https://picsum.photos/seed/cover/1200/400" class="w-full h-full object-cover">
                </div>
                <div class="px-6 -mt-12 md:-mt-16 flex flex-col md:flex-row md:items-end justify-between relative z-10">
                    <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-6">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-bg-primary p-1 border-4 border-bg-primary shadow-xl">
                            <img id="avatarImg" src="" class="w-full h-full rounded-full object-cover shadow-inner bg-bg-secondary">
                        </div>
                        <div class="pb-2">
                            <h1 id="profileName" class="text-2xl md:text-3xl font-bold text-text-primary tracking-tight"></h1>
                            <p id="profileUsername" class="text-text-secondary"></p>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-4 md:mb-2" id="profileActions">
                        <!-- Buttons will be injected via JS -->
                    </div>
                </div>
            </div>

            <!-- Bio & Stats -->
            <div class="px-6 mt-6">
                <p id="profileBio" class="text-sm md:text-base text-text-primary max-w-2xl leading-relaxed"></p>
                <div class="flex space-x-8 mt-6 pb-6 border-b border-border-light">
                    <div class="text-center">
                        <span id="statPosts" class="block text-lg font-bold">0</span>
                        <span class="text-xs text-text-secondary uppercase tracking-widest">Posts</span>
                    </div>
                    <div class="text-center">
                        <span id="statFollowers" class="block text-lg font-bold">0</span>
                        <span class="text-xs text-text-secondary uppercase tracking-widest">Followers</span>
                    </div>
                    <div class="text-center">
                        <span id="statFollowing" class="block text-lg font-bold">0</span>
                        <span class="text-xs text-text-secondary uppercase tracking-widest">Following</span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-border-light sticky top-0 bg-bg-primary z-20 overflow-x-auto no-scrollbar transition-colors">
                <button id="tabPosts" onclick="switchTab('posts')" class="flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] border-b-2 border-primary-500 text-text-primary transition-all duration-300">
                    Neural Feed
                </button>
                <button id="tabMedia" onclick="switchTab('media')" class="flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-text-tertiary hover:text-text-primary transition-all duration-300">
                    Visual Media
                </button>
            </div>

            <!-- Content Views -->
            <div id="postsFeed" class="p-4 md:p-6 space-y-6 max-w-2xl mx-auto">
                <!-- List view for posts -->
            </div>

            <div id="postsGrid" class="hidden grid grid-cols-3 gap-1 md:gap-4 p-1 md:p-6">
                <!-- Grid view for media -->
            </div>

            <div id="gridLoading" class="p-12 flex justify-center">
                <div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </main>

    <div id="editProfileModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="toggleModal('editProfileModal')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl p-4">
            <div class="bg-bg-primary rounded-3xl p-8 shadow-2xl relative animate-scale-in border border-border-light">
                <h3 class="text-2xl font-bold text-text-primary mb-6">Edit Profile</h3>
                
                <div class="space-y-6">
                    <!-- Avatars -->
                    <div class="flex items-center space-x-6">
                        <div class="relative group">
                            <img id="editAvatarPreview" src="" class="w-20 h-20 rounded-full object-cover border-2 border-primary-500 p-0.5">
                            <label class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-full cursor-pointer">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <input type="file" id="avatarUpload" class="hidden" accept="image/*" onchange="previewImage(this, 'editAvatarPreview')">
                            </label>
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Display Name</label>
                            <input type="text" id="editDisplayName" class="w-full h-12 bg-bg-secondary border-2 border-border-light rounded-xl px-4 text-sm focus:border-primary-500 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Bio Transmission</label>
                        <textarea id="editBio" rows="3" class="w-full bg-bg-secondary border-2 border-border-light rounded-xl p-4 text-sm focus:border-primary-500 transition-all resize-none"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Cover Override</label>
                        <label class="flex items-center justify-center w-full h-24 bg-bg-secondary border-2 border-dashed border-border-light rounded-2xl cursor-pointer hover:border-primary-500 transition-all">
                            <span class="text-xs text-text-tertiary">Select new cover sector</span>
                            <input type="file" id="coverUpload" class="hidden" accept="image/*" onchange="window.toast('Cover buffered', 'info')">
                        </label>
                    </div>
                    <div class="space-y-4 pt-4 border-t border-border-light">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-text-primary">Private Account</h4>
                                <p class="text-[10px] text-text-tertiary">Only people you approve can see your posts.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="editIsPrivate" class="sr-only peer">
                                <div class="w-11 h-6 bg-bg-tertiary peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4 mt-8">
                    <button onclick="toggleModal('editProfileModal')" class="flex-1 h-12 bg-bg-secondary text-text-primary rounded-xl text-sm font-bold">CANCEL</button>
                    <button onclick="updateProfile()" class="flex-1 h-12 bg-primary-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-500/20">SYNCHRONIZE</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const targetUsername = @json($username);

    async function loadProfile() {
        try {
            // 1. Fetch User Data
            const endpoint = targetUsername ? `/profile/${targetUsername}` : '/auth/me';
            const data = await window.bridge.request(endpoint);
            const user = data.user;

            // 2. Set UI Elements
            document.getElementById('profileName').innerText = user.display_name || user.username;
            document.getElementById('profileUsername').innerHTML = `@${user.username} ${user.is_private ? '<span class="text-[10px] bg-bg-tertiary text-text-secondary px-2 py-0.5 rounded-full ml-2 border border-border-light font-bold">🔒 PRIVATE</span>' : ''}`;
            document.getElementById('profileBio').innerText = user.bio || 'No bio yet. Connecting to the future...';
            
            // Set avatar with fallback
            const avatarImg = document.getElementById('avatarImg');
            avatarImg.onerror = () => window.handleImageError(avatarImg);
            avatarImg.src = user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.username)}&background=2D3FE6&color=fff&size=200`;
            
            // Set cover with fallback
            const coverImg = document.getElementById('coverImg');
            coverImg.onerror = () => window.handleImageError(coverImg);
            coverImg.src = user.cover_url || 'https://picsum.photos/seed/cover/1200/400';
            
            document.getElementById('statPosts').innerText = user.posts_count || 0;
            document.getElementById('statFollowers').innerText = user.followers_count || 0;
            document.getElementById('statFollowing').innerText = user.following_count || 0;

            // 3. Handle Actions (Edit or Follow)
            const actions = document.getElementById('profileActions');
            if (!targetUsername || (window.currentUser && window.currentUser.username === user.username)) {
                actions.innerHTML = `
                    <button onclick="openEditModal()" class="px-6 py-2 bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 hover:bg-primary-300 transition-all">Edit Profile</button>
                    <button class="px-2 py-2 bg-bg-secondary border border-border-light text-text-primary rounded-xl" onclick="window.bridge.logout()">Logout</button>
                `;
            } else {
                const state = getFollowButtonState(user);
                actions.innerHTML = `
                    <button id="followBtn" class="px-8 py-2 font-bold rounded-xl transition-all ${state.classes}" onclick="toggleFollow('${user.id}')">${state.text}</button>
                    <button class="px-3 py-2 bg-bg-secondary border border-border-light text-text-primary rounded-xl" onclick="startDirectChat('${user.id}')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </button>
                `;
            }

            document.getElementById('profileHeaderLoading').classList.add('hidden');
            document.getElementById('profileContent').classList.remove('hidden');

            // 4. Load Posts
            loadUserPosts(user.id);

        } catch (err) {
            console.error(err);
            window.toast('User not found in the neural network', 'error');
        }
    }

    async function toggleFollow(userId) {
        const btn = document.getElementById('followBtn');
        await toggleFollowUniversal(userId, btn, (res) => {
            // Update stats if needed (wait a bit for animation)
            setTimeout(() => {
                loadProfile(); 
            }, 500);
        });
    }

    async function startDirectChat(userId) {
        try {
            window.toast('Initializing secure link...', 'info');
            const data = await window.bridge.request(`/chats/direct/${userId}`, { method: 'POST' });
            const chat = data.data;
            window.location.href = `/messages/${chat.id}`;
        } catch (err) {
            window.toast('Signal error', 'error');
        }
    }

    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openEditModal() {
        const user = window.currentUser;
        document.getElementById('editDisplayName').value = user.display_name || '';
        document.getElementById('editBio').value = user.bio || '';
        document.getElementById('editAvatarPreview').src = user.avatar_url || `https://ui-avatars.com/api/?name=${user.username}`;
        document.getElementById('editIsPrivate').checked = user.is_private || false;
        toggleModal('editProfileModal');
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => document.getElementById(previewId).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function updateProfile() {
        const data = {
            display_name: document.getElementById('editDisplayName').value,
            bio: document.getElementById('editBio').value,
            is_private: document.getElementById('editIsPrivate').checked
        };
        const avatarFile = document.getElementById('avatarUpload').files[0];
        const coverFile = document.getElementById('coverUpload').files[0];

        try {
            // Update Text
            await window.bridge.request('/profile', {
                method: 'PATCH',
                body: JSON.stringify(data)
            });

            // Update Avatar
            if (avatarFile) {
                const formData = new FormData();
                formData.append('image', avatarFile);
                formData.append('type', 'avatar');
                await window.bridge.request('/profile/image', {
                    method: 'POST',
                    body: formData,
                    headers: {} // FormData handles this
                });
            }

            // Update Cover
            if (coverFile) {
                const formData = new FormData();
                formData.append('image', coverFile);
                formData.append('type', 'cover');
                await window.bridge.request('/profile/image', {
                    method: 'POST',
                    body: formData,
                    headers: {}
                });
            }

            window.toast('Neural profile synchronized', 'success');
            toggleModal('editProfileModal');
            loadProfile();
            initUser(); // Global refresh
        } catch (err) {
            window.toast('Sync failed', 'error');
        }
    }

    let currentTab = 'posts';
    let cachedUserPosts = [];

    function switchTab(tab) {
        currentTab = tab;
        
        // Update Button UI
        const tabPosts = document.getElementById('tabPosts');
        const tabMedia = document.getElementById('tabMedia');
        
        if (tab === 'posts') {
            tabPosts.className = 'flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] border-b-2 border-primary-500 text-text-primary transition-all duration-300';
            tabMedia.className = 'flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-text-tertiary hover:text-text-primary transition-all duration-300';
            document.getElementById('postsFeed').classList.remove('hidden');
            document.getElementById('postsGrid').classList.add('hidden');
        } else {
            tabMedia.className = 'flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] border-b-2 border-primary-500 text-text-primary transition-all duration-300';
            tabPosts.className = 'flex-1 py-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-text-tertiary hover:text-text-primary transition-all duration-300';
            document.getElementById('postsGrid').classList.remove('hidden');
            document.getElementById('postsFeed').classList.add('hidden');
        }
        
        renderProfileContent();
    }

    async function loadUserPosts(userId) {
        document.getElementById('gridLoading').classList.remove('hidden');
        try {
            const data = await window.bridge.request(`/posts?user_id=${userId}`);
            cachedUserPosts = data.data || [];
            renderProfileContent();
        } catch (err) {
            console.error(err);
        } finally {
            document.getElementById('gridLoading').classList.add('hidden');
        }
    }

    function renderProfileContent() {
        if (currentTab === 'posts') {
            renderPosts(cachedUserPosts);
        } else {
            renderGrid(cachedUserPosts);
        }
    }

    window.feedPosts = {}; 
    let feedPlayers = {};
    let viewTimers = {};
    let lastFeedTap = 0;
    let touchStartX = 0;
    let touchStartY = 0;

    function renderPosts(posts) {
        const container = document.getElementById('postsFeed');
        container.innerHTML = '';
        
        // Reset players to prevent duplicates/errors on re-render
        feedPlayers = {};
        
        if (!posts || posts.length === 0) {
            container.innerHTML = '<div class="text-center py-20 text-text-tertiary font-black uppercase tracking-widest italic opacity-50">Neural Feed Empty</div>';
            return;
        }

        posts.forEach(post => {
            window.feedPosts[post.id] = post;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = window.renderPostHtml(post, window.currentUser, 'profile');
            const article = tempDiv.firstElementChild;
            
            // Prepare variables for Player Init
            let videoId = null;
            if (post.media && post.media.length > 0 && post.media[0].type === 'video') {
                    videoId = `video-${post.id}`;
            }
            container.appendChild(article);

            if (videoId) {
                setTimeout(() => {
                    const videoContainer = document.getElementById(videoId);
                    if (videoContainer && !feedPlayers[post.id] && typeof VideoPlayer !== 'undefined') {
                        feedPlayers[post.id] = new VideoPlayer(videoContainer, post.media[0].url, {
                            autoplay: false,
                            muted: true,
                            poster: post.media[0].thumbnail_url,
                            hideControls: true,
                            hideSidebar: true,
                            hideCaption: true
                        });
                        observeFeedVideo(feedPlayers[post.id], videoContainer);
                    }
                }, 100);
            }
        });
    }

    function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }

    async function handleMediaInteraction(postId, event) {
        if (event.type === 'touchend') {
            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            if (Math.abs(touchEndX - touchStartX) > 10 || Math.abs(touchEndY - touchStartY) > 10) return;
        }
        
        const curTime = new Date().getTime();
        const tapLen = curTime - lastFeedTap;
        const post = window.feedPosts[postId];
        const container = event.currentTarget;
        
        if (tapLen < 300 && tapLen > 0) {
            const article = document.getElementById(`post-${postId}`);
            const likeBtn = article.querySelector('.like-btn');
            if (!post.is_liked) toggleLike(postId, likeBtn);
            else showFeedHeart(container);
            lastFeedTap = 0;
            return;
        }
        lastFeedTap = curTime;
    }

    function showFeedHeart(container) {
        const heart = container.querySelector('.quick-like-heart');
        heart.classList.remove('anim-heart');
        void heart.offsetWidth;
        heart.classList.add('anim-heart');
    }

    function handleVideoClick(postId, isReel) {
        if (isReel) window.location.href = `/reels?start=${postId}`;
        else openCommentsSheet(postId, 'profile');
    }

    function observeFeedVideo(player, container) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const postId = container.id.replace('video-', '');
                if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                    pauseAllFeedVideos(postId);
                    if (player && player.video) player.play();
                } else if (player && player.video) {
                    player.pause();
                }
            });
        }, { threshold: [0, 0.5] });
        observer.observe(container);
    }

    function pauseAllFeedVideos(exceptPostId = null) {
        Object.keys(feedPlayers).forEach(postId => {
            if (postId != exceptPostId && feedPlayers[postId]) feedPlayers[postId].pause();
        });
    }

    async function toggleLike(postId, btn) {
        const post = window.feedPosts[postId];
        if (!post || !btn) return;
        const icon = btn.querySelector('svg');
        const countEl = btn.querySelector('.likes-count');
        const wasLiked = post.is_liked;
        const initialCount = post.likes_count;
        
        post.is_liked = !wasLiked;
        post.likes_count = wasLiked ? initialCount - 1 : initialCount + 1;
        
        if (post.is_liked) {
            btn.classList.add('text-accent-500');
            icon.classList.add('fill-accent-500');
            icon.setAttribute('fill', 'currentColor');
            const mediaContainer = document.getElementById(`post-${postId}`).querySelector('.group\\/media');
            if (mediaContainer) showFeedHeart(mediaContainer);
        } else {
            btn.classList.remove('text-accent-500');
            icon.classList.remove('fill-accent-500');
            icon.setAttribute('fill', 'none');
        }
        countEl.innerText = post.likes_count;

        try {
            const data = await window.bridge.request(`/posts/${postId}/like`, { method: 'POST' });
            post.is_liked = data.data.liked;
            post.likes_count = data.data.likes_count;
            countEl.innerText = data.data.likes_count;
        } catch (err) {
            post.is_liked = wasLiked;
            post.likes_count = initialCount;
            countEl.innerText = initialCount;
            window.toast?.('Sync failed - reverted', 'error');
        }
    }

    function renderGrid(posts) {
        const grid = document.getElementById('postsGrid');
        grid.innerHTML = ''; 

        // Filter only posts with image media
        const photoPosts = posts.filter(p => p.media && p.media.length > 0 && p.media[0].type === 'image');

        if (!photoPosts || photoPosts.length === 0) {
            grid.innerHTML = '<div class="col-span-3 text-center py-20 text-text-tertiary font-black uppercase tracking-widest italic opacity-50">No Visual Photos Found</div>';
            return;
        }

        photoPosts.forEach(post => {
            const item = document.createElement('div');
            item.className = 'aspect-square relative group overflow-hidden bg-bg-secondary rounded-[1.5rem] cursor-pointer anim-slide-up border border-white/5';
            item.onclick = () => {
                if (window.openCommentsSheet) window.openCommentsSheet(post.id, 'profile');
            };
            
            const mediaUrl = post.media[0].url;
            
            item.innerHTML = `
                <img src="${mediaUrl}" onerror="handleImageError(this)" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <span class="text-white font-black flex items-center space-x-1 italic">
                            <svg class="w-4 h-4 fill-primary-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            <span>${post.likes_count}</span>
                        </span>
                    </div>
                </div>
            `;
            grid.appendChild(item);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadProfile().catch(err => console.error('loadProfile entry failed:', err));
    });
</script>
@endsection
