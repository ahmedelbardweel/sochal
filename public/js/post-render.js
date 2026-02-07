window.renderPostHtml = function (post, currentUser, context = 'feed') {
    // --- 1. Helper Variables ---
    const isCurrentUser = currentUser && currentUser.id === post.user.id;
    const isHidden = post.status === 'hidden';

    // Follow State Logic (reused from home.blade.php logic)
    // Note: This relies on toggleFollowUniversal handling the UI state update after click
    let followBtnState = { text: 'Follow', linkClasses: 'text-primary-500' };
    if (post.user.is_following) {
        followBtnState = { text: 'Following', linkClasses: 'text-text-tertiary' };
    } else if (post.user.is_requested) {
        followBtnState = { text: 'Requested', linkClasses: 'text-text-tertiary' };
    }

    // --- 2. Hidden Post Banner (Profile Only) ---
    let hiddenBannerHtml = '';
    let opacityClass = '';
    let borderClass = 'border-border-light';

    if (isHidden && isCurrentUser) {
        opacityClass = 'opacity-75';
        borderClass = 'border-dashed border-2 border-text-tertiary';
        hiddenBannerHtml = `
            <div class="bg-bg-tertiary/80 text-center py-2 text-xs font-bold uppercase tracking-widest text-text-secondary border-b border-border-light flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                <span>Hidden from Feed</span>
            </div>
        `;
    }

    // --- 3. Media Rendering ---
    let mediaHtml = '';
    let videoId = null;
    let isReel = false;
    let duration = 0;

    if (post.media && post.media.length > 0) {
        const media = post.media[0];
        duration = media.duration || 0;

        if (media.type === 'video') {
            videoId = `video-${post.id}`;
            const hours = Math.floor(duration / 3600);
            const minutes = Math.floor((duration % 3600) / 60);
            const seconds = Math.floor(duration % 60);
            const durationStr = hours > 0
                ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                : `${minutes}:${seconds.toString().padStart(2, '0')}`;
            isReel = duration <= 180;

            mediaHtml = `
                <div class="relative w-full aspect-[3/2] bg-black cursor-pointer overflow-hidden group/media video-container-feed bg-cover bg-center sm:rounded-xl shadow-inner" 
                     id="${videoId}" 
                     data-url="${media.url}"
                     data-variants='${JSON.stringify(media.variants || {})}'
                     data-duration="${duration}"
                     style="background-image: url('${media.thumbnail_url || '/images/video-placeholder.jpg'}')"
                     onclick="handleVideoClick(${post.id}, ${isReel})">
                     
                     <div class="absolute inset-0 flex items-center justify-center bg-black/10 group-hover/media:bg-black/30 transition-all">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-md border border-white/30 transform group-hover/media:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                     </div>

                     <div class="absolute bottom-3 right-3 px-2 py-1 rounded-md bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold">
                        ${durationStr}
                     </div>
                     <div class="quick-like-heart absolute inset-0 flex items-center justify-center opacity-0 pointer-events-none z-20 will-change-transform">
                        <svg class="w-24 h-24 text-white fill-white drop-shadow-2xl shadow-white/50" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                     </div>
                </div>`;
        } else {
            mediaHtml = `
                <div class="relative aspect-square md:aspect-video w-full bg-black cursor-pointer overflow-hidden group/media" 
                     onmousedown="handleMediaInteraction(${post.id}, event)"
                     ontouchstart="handleTouchStart(event)"
                     ontouchend="handleMediaInteraction(${post.id}, event)">
                     <img src="${media.url}" class="w-full h-full object-contain pointer-events-none" onerror="handleImageError(this)">
                     <div class="quick-like-heart absolute inset-0 flex items-center justify-center opacity-0 pointer-events-none z-20 will-change-transform">
                        <svg class="w-24 h-24 text-white fill-white drop-shadow-2xl shadow-white/50" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                     </div>
                   </div>`;
        }
    } else {
        // Fallback or text-only (if supported)
        mediaHtml = `<div class="aspect-video bg-bg-tertiary flex items-center justify-center text-text-tertiary">No Media</div>`;
    }

    // --- 4. Render Article ---
    return `
        <article id="post-${post.id}" class="bg-bg-secondary rounded-2xl border ${borderClass} overflow-hidden shadow-sm hover:shadow-md transition-shadow mb-8 anim-slide-in ${opacityClass}">
            ${hiddenBannerHtml}
            
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center space-x-3 cursor-pointer group/user" onclick="window.location.href='/profile/${post.user.username}'">
                    <img src="${post.user.avatar_url || 'https://ui-avatars.com/api/?name=' + post.user.username}" 
                         onerror="handleImageError(this)"
                         class="w-10 h-10 rounded-full border border-border-light object-cover group-hover/user:border-primary-500/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-black text-white italic truncate">${post.user.display_name || post.user.username}</h3>
                            ${!isCurrentUser ? `
                                <button onclick="event.stopPropagation(); toggleFollowUniversal(${post.user.id}, this)" 
                                        class="font-bold text-[11px] hover:text-primary-400 transition-colors ml-2 ${followBtnState.linkClasses}">
                                    ${followBtnState.text}
                                </button>
                            ` : ''}
                        </div>
                        <p class="text-[10px] text-text-tertiary truncate">@${post.user.username}</p>
                    </div>
                </div>
                <div class="relative">
                    <button onclick="event.stopPropagation(); togglePostMenu(${post.id})" class="p-2 text-text-tertiary hover:text-text-primary hover:bg-white/5 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="menu-${post.id}" class="hidden absolute right-0 top-10 w-48 bg-bg-secondary border border-border-light rounded-2xl shadow-xl z-50 overflow-hidden anim-scale-in origin-top-right">
                        ${isCurrentUser ? `
                            <button onclick="event.stopPropagation(); window.openEditPostModal(${post.id})" class="w-full text-left px-4 py-3 text-sm font-bold text-text-primary hover:bg-white/5 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit Post</span>
                            </button>
                            <button onclick="event.stopPropagation(); window.togglePostVisibility(${post.id}, '${post.status}')" class="w-full text-left px-4 py-3 text-sm font-bold text-text-primary hover:bg-white/5 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${post.status === 'hidden' ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'}"/></svg>
                                <span>${post.status === 'hidden' ? 'Unhide' : 'Hide from Feed'}</span>
                            </button>
                            <button onclick="event.stopPropagation(); window.deletePost(${post.id})" class="w-full text-left px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-500/10 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Delete</span>
                            </button>
                        ` : `
                            <button onclick="event.stopPropagation(); window.hidePostClient(${post.id})" class="w-full text-left px-4 py-3 text-sm font-bold text-text-primary hover:bg-white/5 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                <span>Hide Post</span>
                            </button>
                            <button onclick="event.stopPropagation(); window.openReportModal(${post.id}, 'post')" class="w-full text-left px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-500/10 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8l-2 2H5l-2-2z"/></svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8l-2 2H5l-2-2zM5 21V7a2 2 0 012-2h14a2 2 0 012 2v8l-2 2H5"/></svg>
                                <span>Report</span>
                            </button>
                        `}
                    </div>
                </div>
            </div>
            
            ${mediaHtml}

            <div class="px-4 pt-4">
                <p class="text-sm text-text-primary leading-relaxed">${post.caption || ''}</p>
                ${post.location ? `<p class="text-[10px] text-primary-500 mt-1 font-bold">📍 ${post.location}</p>` : ''}
            </div>

            <div class="p-4 flex items-center space-x-6">
                <button onclick="toggleLike(${post.id}, this)" class="like-btn flex items-center space-x-2 ${post.is_liked ? 'text-accent-500' : 'text-text-secondary hover:text-accent-500'} transition-all active:scale-90 group">
                    <svg class="w-5 h-5 ${post.is_liked ? 'fill-accent-500' : ''}" fill="${post.is_liked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="text-xs font-bold likes-count">${post.likes_count}</span>
                </button>
                <button onclick="openCommentsSheet(${post.id})" class="flex items-center space-x-2 text-text-secondary hover:text-primary-500 transition-all active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span class="text-xs font-bold">${post.comments_count}</span>
                </button>
            </div>
        </article>
    `;
};
