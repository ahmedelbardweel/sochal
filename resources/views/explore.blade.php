@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary text-text-primary">
    @include('partials.sidebar')

    <main class="flex-1 pb-20 min-w-0 overflow-hidden">
        <!-- Professional Search Header -->
        <div class="sticky top-0 z-30 bg-bg-primary/80 backdrop-blur-xl border-b border-border-light px-4 py-3 md:py-4">
            <div class="max-w-3xl mx-auto relative group">
                <input type="text" id="globalSearch" placeholder="Search friends, creators, or tags..." 
                    class="w-full h-11 md:h-12 bg-bg-secondary border border-border-light focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl px-12 text-base md:text-sm transition-all duration-300 outline-none shadow-sm group-hover:shadow-md">
                <svg class="absolute left-4 top-3.5 w-5 h-5 text-text-tertiary group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <div id="searchLoader" class="absolute right-4 top-3.5 hidden">
                    <div class="w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8">
            <!-- Dynamic Sections Container -->
            <div id="exploreContent">
                <!-- Suggested People (Shown when not searching) -->
                <section id="suggestedSection" class="mb-8 md:mb-12">
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <div>
                            <h2 class="text-lg md:text-xl font-black tracking-tight italic uppercase">Neural Discovery</h2>
                            <p class="text-[10px] md:text-xs text-text-tertiary">Suggested creators for your network</p>
                        </div>
                        <button onclick="showAllTrending()" class="text-xs font-bold text-primary-500 hover:underline">See All</button>
                    </div>
                    <div id="suggestedCarousel" class="flex space-x-4 overflow-x-auto pb-6 scrollbar-hide no-scrollbar">
                        <!-- Suggested users rendered server-side -->
                        @forelse($suggestedUsers ?? [] as $index => $user)
                        <div onclick="window.location.href='/profile/{{ $user->username }}'" 
                             class="flex-shrink-0 w-52 md:w-60 bg-bg-secondary/40 backdrop-blur-md border border-border-light rounded-[2rem] p-6 flex flex-col items-center text-center cursor-pointer hover:bg-bg-secondary/60 hover:border-primary-500/30 transition-all duration-500 group relative overflow-hidden anim-slide-up"
                             style="animation-delay: {{ $index * 50 }}ms">
                            
                            <!-- Premium Glow Effect -->
                            <div class="absolute -top-10 -right-10 w-24 h-24 bg-primary-500/10 blur-3xl rounded-full group-hover:bg-primary-500/20 transition-all"></div>
                            
                            <div class="relative mb-4">
                                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full p-1 bg-gradient-to-tr from-primary-500 via-accent-500 to-primary-500 animate-gradient-xy group-hover:scale-105 transition-transform duration-500">
                                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->username) }}" 
                                         onerror="handleImageError(this)"
                                         class="w-full h-full rounded-full object-cover border-4 border-bg-secondary">
                                </div>
                                @if($user->is_verified)
                                <div class="absolute bottom-1 right-1 w-7 h-7 bg-primary-500 text-white rounded-full flex items-center justify-center border-4 border-bg-secondary shadow-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <div class="w-full mb-1 flex items-center justify-center space-x-1">
                                <h4 class="font-black text-sm md:text-base text-text-primary truncate max-w-[140px] tracking-tight italic uppercase">{{ $user->display_name ?? $user->username }}</h4>
                                @if($user->is_verified)
                                    <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                @endif
                            </div>
                            <p class="text-[10px] md:text-xs text-text-tertiary mb-3 font-medium">@{{ $user->username }}</p>
                            
                            <!-- Follower Status Badge -->
                            <div class="px-3 py-1 bg-bg-tertiary/20 rounded-full border border-border-light text-[10px] font-bold text-text-secondary mb-5">
                                {{ number_format($user->followers_count ?? 0) }} FOLLOWERS
                            </div>

                            <button onclick="event.stopPropagation(); followUser({{ $user->id }}, this)" 
                                class="w-full py-2.5 bg-primary-500 text-white rounded-2xl text-[10px] md:text-xs font-black tracking-widest uppercase hover:brightness-110 hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary-500/20">
                                {{ $user->is_following ? 'FOLLOWING' : 'FOLLOW' }}
                            </button>
                        </div>
                        @empty
                        <div class="w-full py-10 flex flex-col items-center justify-center text-text-tertiary">
                            <p class="text-xs">No trending creators found</p>
                        </div>
                        @endforelse
                    </div>
                </section>

                <!-- Discovery Grid / Search Results -->
                <section>
                    <div id="gridHeader" class="flex items-center justify-between mb-4 md:mb-6">
                        <h2 class="text-lg md:text-xl font-black tracking-tight italic uppercase" id="sectionTitle">Explore Grid</h2>
                        <div class="flex bg-bg-secondary p-1 rounded-xl border border-border-light">
                            <button id="viewPosts" class="px-3 md:px-4 py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all bg-primary-500 text-white shadow-sm">POSTS</button>
                            <button id="viewPeople" class="px-3 md:px-4 py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all text-text-tertiary hover:text-text-primary">PEOPLE</button>
                        </div>
                    </div>

                    <div id="resultsGrid" class="columns-2 md:columns-3 lg:columns-4 gap-2 md:gap-4 space-y-2 md:space-y-4">
                        <!-- Grid items live here -->
                    </div>
                    
                    <!-- Search Results Specific Container (Hidden by default) -->
                    <div id="userResults" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- User cards live here -->
                    </div>
                </section>
            </div>

            <!-- Empty / No Results -->
            <div id="emptyState" class="hidden flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-bg-secondary rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold">No results found</h3>
                <p class="text-sm text-text-tertiary max-w-xs">We couldn't find anything matching your query in the neural net.</p>
            </div>

            <div id="loadMoreTrigger" class="h-20 flex items-center justify-center">
                <div id="gridLoader" class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </main>
</div>


<!-- Styles for specific animations and layouts -->
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    #resultsGrid > div { break-inside: avoid; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    
    @keyframes gradient-xy {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .animate-gradient-xy { background-size: 200% 200%; animation: gradient-xy 3s ease infinite; }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim-slide-up { animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

<script>
    let nextCursor = null;
    let isLoading = false;
    let currentView = 'posts'; // posts or people
    let searchDebounce = null;
    let query = new URLSearchParams(window.location.search).get('q') || '';

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('globalSearch');
        if (query) {
            searchInput.value = query;
            handleSearch(query);
        } else {
            // loadSuggestions(); // Disabled: users are now server-rendered
            loadExplore(true);
        }

        // Auto-open post detail if post_id is present
        const urlParams = new URLSearchParams(window.location.search);
        const postId = urlParams.get('post_id');
        if (postId) {
             // Auto-open logic removed as per request to decommission modal
        }

        // Live Search with Debounce
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchDebounce);
            const val = e.target.value.trim();
            searchDebounce = setTimeout(() => {
                query = val;
                handleSearch(val);
            }, 500);
        });

        // View Toggling
        document.getElementById('viewPosts').onclick = () => switchView('posts');
        document.getElementById('viewPeople').onclick = () => switchView('people');

        // Infinite Scroll
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && nextCursor && !isLoading) loadExplore();
        }, { threshold: 0.1 });
        observer.observe(document.getElementById('loadMoreTrigger'));
    });

    function switchView(view) {
        currentView = view;
        document.getElementById('viewPosts').className = view === 'posts' ? 'px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-primary-500 text-white shadow-sm' : 'px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-text-tertiary hover:text-text-primary';
        document.getElementById('viewPeople').className = view === 'people' ? 'px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-primary-500 text-white shadow-sm' : 'px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-text-tertiary hover:text-text-primary';
        
        handleSearch(query);
    }

    async function handleSearch(val) {
        if (!val) {
            window.history.replaceState({}, '', '/explore');
            document.getElementById('suggestedSection')?.classList.remove('hidden');
            document.getElementById('sectionTitle').innerText = 'Explore Grid';
            loadExplore(true);
            return;
        }

        window.history.replaceState({}, '', `/explore?q=${encodeURIComponent(val)}`);
        document.getElementById('suggestedSection').classList.add('hidden');
        document.getElementById('sectionTitle').innerText = `Search: ${val}`;
        
        // When searching, we usually want people first or filtered posts
        // For this professional version, if there's a query, we show what's selected
        loadExplore(true);
    }

    async function loadSuggestions() {
        try {
            const data = await window.bridge.request('/profile/suggested');
            if (data && data.data) {
                renderSuggestions(data.data);
            } else {
                renderSuggestions([]); // Force empty state if no data
            }
        } catch (err) {
            console.error('Suggestions error:', err);
            renderSuggestions([]); // Clear skeletons on error
        }
    }

    function renderSuggestions(users) {
        const container = document.getElementById('suggestedCarousel');
        if (!users || !Array.isArray(users) || users.length === 0) {
            container.innerHTML = `
                <div class="w-full py-10 flex flex-col items-center justify-center text-text-tertiary">
                    <p class="text-xs">Finding the best creators for your neural network...</p>
                </div>
            `;
            return;
        }

        container.innerHTML = users.map((user, index) => `
            <div onclick="window.location.href='/profile/${user.username}'" 
                 class="flex-shrink-0 w-52 md:w-60 bg-bg-secondary/40 backdrop-blur-md border border-border-light rounded-[2rem] p-6 flex flex-col items-center text-center cursor-pointer hover:bg-bg-secondary/60 hover:border-primary-500/30 transition-all duration-500 group relative overflow-hidden anim-slide-up"
                 style="animation-delay: ${index * 50}ms">
                
                <!-- Premium Glow Effect -->
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-primary-500/10 blur-3xl rounded-full group-hover:bg-primary-500/20 transition-all"></div>
                
                <div class="relative mb-4">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full p-1 bg-gradient-to-tr from-primary-500 via-accent-500 to-primary-500 animate-gradient-xy group-hover:scale-105 transition-transform duration-500">
                        <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" 
                             onerror="handleImageError(this)"
                             class="w-full h-full rounded-full object-cover border-4 border-bg-secondary">
                    </div>
                    ${user.is_verified ? `
                    <div class="absolute bottom-1 right-1 w-7 h-7 bg-primary-500 text-white rounded-full flex items-center justify-center border-4 border-bg-secondary shadow-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    ` : ''}
                </div>

                <div class="w-full mb-1 flex items-center justify-center space-x-1">
                    <h4 class="font-black text-sm md:text-base text-text-primary truncate max-w-[140px] tracking-tight italic uppercase">${user.display_name || user.username}</h4>
                    ${user.is_verified ? `
                        <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                    ` : ''}
                </div>
                <p class="text-[10px] md:text-xs text-text-tertiary mb-3 font-medium">@${user.username}</p>
                
                <!-- Follower Status Badge -->
                <div class="px-3 py-1 bg-bg-tertiary/20 rounded-full border border-border-light text-[10px] font-bold text-text-secondary mb-5">
                    ${(user.followers_count || 0).toLocaleString()} FOLLOWERS
                </div>

                <button onclick="event.stopPropagation(); followUser(${user.id}, this)" 
                    class="w-full py-2.5 bg-primary-500 text-white rounded-2xl text-[10px] md:text-xs font-black tracking-widest uppercase hover:brightness-110 hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary-500/20">
                    ${user.is_following ? 'FOLLOWING' : 'FOLLOW'}
                </button>
            </div>
        `).join('');
    }

    async function loadExplore(reset = false) {
        if (isLoading) return;
        isLoading = true;
        const loader = document.getElementById('searchLoader');
        if (loader) loader.classList.remove('hidden');
        
        if (reset) {
            document.getElementById('resultsGrid').innerHTML = '';
            document.getElementById('userResults').innerHTML = '';
            document.getElementById('emptyState').classList.add('hidden');
            nextCursor = null;
        }

        try {
            let endpoint;
            if (query) {
                if (currentView === 'people') {
                    endpoint = `/search/users?q=${encodeURIComponent(query)}&page=${nextCursor || 1}`;
                } else {
                    endpoint = `/posts?q=${encodeURIComponent(query)}&cursor=${nextCursor || ''}`;
                }
            } else {
                if (currentView === 'people') {
                    endpoint = '/profile/suggested';
                } else {
                    endpoint = `/posts/discovery?cursor=${nextCursor || ''}`;
                }
            }

            const res = await window.bridge.request(endpoint);
            
            if (currentView === 'people') {
                const results = res.data || res.users?.data || [];
                renderUserList(results);
                nextCursor = res.next_page_url ? (new URL(res.next_page_url).searchParams.get('page')) : (res.current_page < res.last_page ? res.current_page + 1 : null);
            } else {
                const posts = res?.posts ? res.posts.data : (res?.data || []);
                renderPostGrid(posts);
                nextCursor = res?.posts ? res.posts.next_cursor : (res.next_cursor || null);
            }

            // Check empty
            const hasData = document.getElementById('resultsGrid').children.length > 0 || 
                            document.getElementById('userResults').children.length > 0;
            if (!hasData) document.getElementById('emptyState').classList.remove('hidden');

        } catch (err) {
            console.error('Explore error:', err);
            document.getElementById('emptyState').classList.remove('hidden');
        } finally {
            isLoading = false;
            if (loader) loader.classList.add('hidden');
            const gridLoader = document.getElementById('gridLoader');
            if (gridLoader) gridLoader.classList.add(nextCursor ? 'block' : 'hidden');
        }
    }

    let feedPosts = {}; // Global map for explore posts

    function renderPostGrid(posts) {
        document.getElementById('resultsGrid').classList.remove('hidden');
        document.getElementById('userResults').classList.add('hidden');
        
        const grid = document.getElementById('resultsGrid');
        const viewObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const postId = entry.target.dataset.id;
                    window.bridge.request(`/posts/${postId}/view`, { method: 'POST' }).catch(() => {});
                    viewObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        posts.forEach(post => {
            if (!post.media || post.media.length === 0) return;
            
            const item = document.createElement('div');
            item.dataset.id = post.id;
            item.className = 'group relative rounded-2xl overflow-hidden cursor-pointer bg-bg-secondary shadow-sm hover:shadow-2xl transition-all duration-500 mb-2 md:mb-4 anim-slide-in';
            item.setAttribute('onmousedown', `handleMediaInteraction(${post.id}, event)`);
            item.setAttribute('ontouchstart', 'handleTouchStart(event)');
            item.setAttribute('ontouchend', `handleMediaInteraction(${post.id}, event)`);
            item.innerHTML = `
                <img src="${post.media[0].url}" 
                     onerror="handleImageError(this)"
                     class="w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-3 md:p-5">
                    <div class="flex items-center space-x-2 md:space-x-3 mb-1 md:mb-2">
                        <img src="${post.user.avatar_url || 'https://ui-avatars.com/api/?name='+post.user.username}" 
                             onerror="handleImageError(this)"
                             class="w-6 h-6 md:w-8 md:h-8 rounded-full border border-white/40">
                        <div class="overflow-hidden">
                            <p class="text-[10px] md:text-xs text-white font-black truncate">@${post.user.username}</p>
                            <p class="text-[8px] md:text-[10px] text-white/70 truncate">${post.location || ''}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center space-x-3 md:space-x-4">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 md:w-4 h-3 md:h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                <span class="text-[10px] md:text-xs font-bold">${post.likes_count || 0}</span>
                            </div>
                        </div>
                        ${post.media[0].type === 'video' && (!post.media[0].duration || post.media[0].duration <= 180) ? `
                        <div class="absolute top-2 right-2 bg-black/50 p-1.5 rounded-full backdrop-blur-sm">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h12a1 1 0 001-1v-3.5l4 4v-11l-4 4z"/></svg>
                        </div>
                        ` : ''}
                        <div class="flex items-center space-x-1 opacity-70">
                            <svg class="w-3 md:w-4 h-3 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span class="text-[10px] md:text-[xs]">${post.views_count || 0}</span>
                        </div>
                    </div>
                </div>
            `;
            feedPosts[post.id] = post;
            grid.appendChild(item);
            viewObserver.observe(item);
        });
    }

    let lastExploreTap = 0;
    let touchStartX = 0;
    let touchStartY = 0;

    function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }

    async function handleMediaInteraction(postId, event) {
        if (event.type === 'touchend') {
            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            const dx = Math.abs(touchEndX - touchStartX);
            const dy = Math.abs(touchEndY - touchStartY);
            if (dx > 10 || dy > 10) return;
            if (event.cancelable) event.preventDefault();
        }

        const curTime = new Date().getTime();
        const tapLen = curTime - lastExploreTap;
        const post = feedPosts[postId];
        
        if (tapLen < 300 && tapLen > 0) {
            lastExploreTap = 0;
            return;
        }
        
        lastExploreTap = curTime;
        setTimeout(() => {
            if (new Date().getTime() - lastExploreTap >= 300) {
                // Check for Reel/Video type
                const video = post.media?.find(m => m.type === 'video');
                if (post.type === 'video' || post.type === 'reel' || video) {
                    // Navigate to Immersive Player (Reels page)
                    window.location.href = `/reels?start=${post.id}`;
                } else {
                    if (window.openCommentsSheet) window.openCommentsSheet(post.id, 'explore');
                }
            }
        }, 300);
    }



    function renderUserList(users) {
        document.getElementById('resultsGrid').classList.add('hidden');
        document.getElementById('userResults').classList.remove('hidden');
        
        const container = document.getElementById('userResults');
        users.forEach(user => {
            const card = document.createElement('div');
            card.className = 'bg-bg-secondary border border-border-light rounded-3xl p-3 md:p-4 flex items-center space-x-3 md:space-x-4 hover:border-primary-500 transition-all duration-300 cursor-pointer group';
            card.innerHTML = `
                <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" 
                     onerror="handleImageError(this)"
                     class="w-12 h-12 md:w-16 md:h-16 rounded-full object-cover">
                <div class="flex-1 overflow-hidden">
                    <div class="flex items-center space-x-1">
                        <h4 class="font-bold text-xs md:text-sm truncate">${user.display_name}</h4>
                        ${user.is_verified ? `
                            <svg class="w-3.5 h-3.5 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                        ` : ''}
                    </div>
                    <p class="text-[10px] md:text-xs text-primary-500 mb-1">@${user.username}</p>
                    <p class="text-[8px] md:text-[10px] text-text-tertiary truncate">${user.followers_count || 0} Followers • ${user.posts_count || 0} Posts</p>
                </div>
                ${(() => {
                    const state = getFollowButtonState(user);
                    return `<button onclick="event.stopPropagation(); followUser(${user.id}, this)" class="px-3 md:px-4 py-1.5 rounded-xl text-[10px] md:text-xs font-black transition-all ${state.classes}">${state.text}</button>`;
                })()}
            `;
            card.onclick = () => window.location.href = `/profile/${user.username}`;
            container.appendChild(card);
        });
    }

    async function followUser(userId, btn) {
        await toggleFollowUniversal(userId, btn);
    }

    async function showAllTrending() {
        // Switch view first, which triggers loadExplore
        switchView('people');
        // Update title after a small delay to override default
        setTimeout(() => {
            document.getElementById('sectionTitle').innerText = 'Trending Creators';
        }, 50);
    }
</script>
@endsection
