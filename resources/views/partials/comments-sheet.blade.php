<!-- Unified Comments Bottom Sheet (Premium Glassmorphism) -->
<div id="commentsSheet" class="fixed inset-0 z-[120] hidden overflow-hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-500" id="commentsSheetBackdrop" onclick="closeCommentsSheet()"></div>
    
    <!-- Sheet Container -->
    <div class="absolute bottom-0 left-0 right-0 max-w-2xl mx-auto h-[75vh] bg-bg-primary/95 backdrop-blur-3xl rounded-t-[2.5rem] border-t border-border-light shadow-2xl dark:shadow-[0_-20px_50px_rgba(0,0,0,0.5)] transform translate-y-full transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] flex flex-col" id="commentsSheetContent">
        
        <!-- Drag Handle Indicator -->
        <div class="w-full flex justify-center pt-3 pb-1" onclick="closeCommentsSheet()">
            <div class="w-12 h-1.5 bg-text-tertiary/20 rounded-full cursor-pointer hover:bg-text-tertiary/40 transition-colors"></div>
        </div>

        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-border-light">
            <div class="flex flex-col">
                <h3 class="text-sm font-black text-text-primary italic tracking-tight uppercase">Neural Signals</h3>
                <p id="commentsSheetSubtitle" class="text-[9px] text-primary-500 font-bold tracking-widest uppercase opacity-80">Syncing responses...</p>
            </div>
            <button onclick="closeCommentsSheet()" class="p-2 hover:bg-bg-tertiary rounded-2xl transition-all active:scale-95 text-text-secondary hover:text-text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Comments List -->
        <div id="commentsSheetList" class="flex-1 overflow-y-auto px-6 py-4 space-y-6 no-scrollbar">
            <!-- Dynamic comments injected here -->
        </div>

        <!-- Input Area (Sticky Footer) -->
        <div class="p-6 bg-bg-primary/50 backdrop-blur-md border-t border-border-light safe-area-bottom">
            <div class="relative group">
                <div class="absolute inset-0 bg-primary-500/10 rounded-2xl blur-md group-focus-within:bg-primary-500/20 transition-all"></div>
                <div class="relative flex items-center bg-bg-secondary/60 border border-border-light rounded-2xl px-5 py-2.5 focus-within:border-primary-500/40 transition-all shadow-inner">
                    <input type="text" id="commentsSheetInput" placeholder="Add a response..." 
                        class="flex-1 bg-transparent border-none py-2 text-base outline-none placeholder:text-text-tertiary text-text-primary font-medium"
                        onkeypress="if(event.key==='Enter') postSheetComment()">
                    <button onclick="postSheetComment()" class="ml-3 text-primary-500 font-black text-[11px] uppercase tracking-[0.15em] hover:text-primary-400 active:scale-95 transition-all disabled:opacity-50" id="commentsSheetSubmit">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSheetPostId = null;
let sheetSource = null; // 'feed', 'reels', 'stories'

async function openCommentsSheet(postId, source = 'feed') {
    currentSheetPostId = postId;
    sheetSource = source;
    
    const sheet = document.getElementById('commentsSheet');
    const backdrop = document.getElementById('commentsSheetBackdrop');
    const content = document.getElementById('commentsSheetContent');
    const list = document.getElementById('commentsSheetList');
    const subtitle = document.getElementById('commentsSheetSubtitle');

    // Reset UI
    sheet.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate In
    setTimeout(() => {
        backdrop.classList.add('opacity-100');
        content.classList.remove('translate-y-full');
    }, 10);

    // Initial Loading State
    list.innerHTML = Array(4).fill(0).map(() => `
        <div class="flex items-start space-x-4 animate-pulse">
            <div class="w-10 h-10 rounded-full bg-bg-tertiary/20"></div>
            <div class="flex-1 space-y-3">
                <div class="h-2.5 w-24 bg-bg-tertiary/20 rounded"></div>
                <div class="h-4 w-full bg-bg-tertiary/20 rounded-lg"></div>
            </div>
        </div>
    `).join('');

    try {
        const res = await window.bridge.request(`/posts/${postId}/comments`);
        const comments = res.data || [];
        
        subtitle.innerText = `${comments.length} SIGNAL${comments.length !== 1 ? 'S' : ''} DETECTED`;

        if (comments.length === 0) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 opacity-40 animate-fade-in">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">No signals translated yet</p>
                </div>`;
            return;
        }

        list.innerHTML = comments.map(c => `
            <div class="flex items-start space-x-4 group/comment animate-slide-in">
                <img src="${c.user.avatar_url || 'https://ui-avatars.com/api/?name='+c.user.username}" 
                     class="w-10 h-10 rounded-full border border-border-light p-0.5 object-cover bg-bg-tertiary">
                <div class="flex-1">
                    <div class="bg-bg-secondary rounded-[1.5rem] p-4 border border-border-light group-hover/comment:bg-bg-tertiary group-hover/comment:border-primary-500/20 transition-all">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-[11px] font-black text-primary-500 uppercase tracking-widest italic">@${c.user.username}</h4>
                            <span class="text-[9px] text-text-tertiary font-bold opacity-60 italic">${formatTime(c.created_at)}</span>
                        </div>
                        <p class="text-[13px] text-text-primary leading-relaxed font-medium">${c.comment}</p>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) {
        list.innerHTML = `<div class="text-center py-10 text-red-500 text-[10px] font-black uppercase tracking-widest">Feedback Uplink Offline</div>`;
    }
}

function closeCommentsSheet() {
    const backdrop = document.getElementById('commentsSheetBackdrop');
    const content = document.getElementById('commentsSheetContent');
    const sheet = document.getElementById('commentsSheet');

    backdrop.classList.remove('opacity-100');
    content.classList.add('translate-y-full');
    
    setTimeout(() => {
        sheet.classList.add('hidden');
        document.body.style.overflow = '';
        currentSheetPostId = null;
    }, 500);
}

async function postSheetComment() {
    const input = document.getElementById('commentsSheetInput');
    const btn = document.getElementById('commentsSheetSubmit');
    const val = input.value.trim();
    if (!val || !currentSheetPostId || btn.disabled) return;
    
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = 'SYNCING...';
    
    try {
        await window.bridge.request(`/posts/${currentSheetPostId}/comments`, {
            method: 'POST',
            body: JSON.stringify({ comment: val })
        });
        
        input.value = '';
        input.blur();
        
        // Refresh comments list
        await openCommentsSheet(currentSheetPostId, sheetSource);
        
        // Sync comment counts globally
        updateGlobalCommentCounts(currentSheetPostId);
        
    } catch (err) {
        window.toast?.('Comment sync failed', 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
    }
}

function updateGlobalCommentCounts(postId) {
    // 1. Home Feed Map
    if (typeof window.feedPosts !== 'undefined' && window.feedPosts[postId]) {
        window.feedPosts[postId].comments_count++;
        // Update feed UI
        const article = document.getElementById(`post-${postId}`);
        if (article) {
            const countBtn = article.querySelector('button[onclick*="openPostModal"] span');
            if (countBtn) countBtn.innerText = window.feedPosts[postId].comments_count;
        }
    }
    
    // 2. Reels Map
    if (typeof reels !== 'undefined') {
        const reel = reels.find(r => r.id == postId);
        if (reel) reel.comments_count++;
        // If Reels player UI exists, it might need updating (but Reels sidebar doesn't show counts yet)
    }
}

function formatTime(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = (now - date) / 1000;

    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
}
</script>

<style>
.safe-area-bottom {
    padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px));
}
.animate-slide-in {
    animation: sheetSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes sheetSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
