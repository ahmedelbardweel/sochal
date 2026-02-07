@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary">
    @include('partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-xl mx-auto pb-20 md:pb-0">
        <!-- New Mobile Header -->
        <header class="sticky top-0 z-50 flex items-center justify-between px-4 py-3 bg-bg-primary/80 backdrop-blur-md border-b border-border-light md:hidden">
            <!-- Spacer for visual balance -->
            <div class="w-6"></div>

            <!-- Professional Hollow Logo -->
            <div class="flex items-center justify-center">
                <svg class="w-8 h-8 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" /> 
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>

            <!-- Settings Icon -->
            <a href="{{ route('settings') }}" class="text-text-secondary hover:text-text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>
        </header>

        <div class="px-4 pt-4 pb-0 lg:px-8">
            <!-- Stories Area -->
            <div class="flex space-x-4 overflow-x-auto pb-4 scrollbar-hide no-scrollbar" id="storiesRail">
                <!-- Stories will be injected here -->
            </div>
            
            <!-- Hidden input for adding stories (Moved outside to persist) -->
            <input type="file" id="storyUpload" class="hidden" accept="image/*" onchange="uploadStory(this)">

            <!-- Post Creator Trigger -->
            <a href="{{ route('create') }}" class="block bg-bg-secondary rounded-xl border border-border-light p-3.5 mb-3 cursor-pointer hover:border-primary-500/50 transition-all group shadow-sm">
                <div class="flex items-center space-x-3">
                    <img src="https://ui-avatars.com/api/?name=User&background=random" class="w-8 h-8 rounded-lg" id="userAvatarSmall">
                    <div class="flex-1 bg-bg-primary/50 rounded-lg px-3 py-1.5 text-text-tertiary text-xs">
                        What's on your mind? #AbsScroll
                    </div>
                </div>
            </a>
        </div>

        <!-- Sticky Tabs Header -->
        <div class="sticky top-14 md:top-0 bg-bg-primary/90 backdrop-blur-xl z-[40] border-b border-border-light px-4 lg:px-8 transition-all">
            <div class="flex">
                <button onclick="switchFeed('discovery')" id="btn-discovery" class="flex-1 py-3 text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 border-b-2 border-primary-500 text-text-primary">
                    For You
                </button>
                <button onclick="switchFeed('following')" id="btn-following" class="flex-1 py-3 text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 border-b-2 border-transparent text-text-tertiary hover:text-text-primary">
                    Following
                </button>
            </div>
        </div>

        <div class="px-4 py-6 lg:px-8">
            <!-- Shimmer Loading -->
            <div id="feedLoading" class="space-y-6 mb-6">
                @for ($i = 1; $i <= 3; $i++)
                <div class="bg-bg-secondary rounded-2xl border border-border-light p-4 animate-pulse">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-bg-tertiary rounded-full"></div>
                        <div class="flex-1 space-y-2"><div class="h-3 bg-bg-tertiary rounded w-1/4"></div><div class="h-2 bg-bg-tertiary rounded w-1/6"></div></div>
                    </div>
                    <div class="h-4 bg-bg-tertiary rounded w-3/4 mb-4"></div>
                    <div class="aspect-video bg-bg-tertiary rounded-xl"></div>
                </div>
                @endfor
            </div>

            <!-- Feed Container -->
            <div id="feedContainer" class="space-y-6"></div>
            
            <div id="loadMoreTrigger" class="h-10"></div>
        </div>
    </main>

    <!-- Right Widgets -->
    <aside class="hidden lg:flex flex-col w-72 h-screen sticky top-0 px-5 py-6 space-y-6">
        <div class="relative">
            <input type="text" placeholder="Search AbsScroll" class="w-full h-10 bg-bg-secondary border border-border-light rounded-lg px-4 pl-10 text-xs">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="bg-bg-secondary rounded-2xl border border-border-light p-4">
            <h3 class="font-bold text-text-primary mb-4">Trending for you</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] text-text-secondary tracking-widest uppercase">Tech • Trending</p>
                    <p class="text-sm font-bold text-text-primary">#AbsScrollBeta</p>
                    <p class="text-[10px] text-text-secondary">4.2k posts</p>
                </div>
                <div>
                    <p class="text-[10px] text-text-secondary tracking-widest uppercase">Design • Trending</p>
                    <p class="text-sm font-bold text-text-primary">#Glassmorphism</p>
                    <p class="text-[10px] text-text-secondary">2.1k posts</p>
                </div>
            </div>
        </div>
    </aside>

</div>


<!-- Immersive Story Viewer -->
<div id="storyViewer" class="fixed inset-0 z-[100] bg-black hidden flex-col items-center justify-center anim-fade-in touch-none">
    <div id="storyBackdrop" class="absolute inset-0 bg-cover bg-center blur-3xl opacity-30 scale-110 pointer-events-none transition-all duration-700"></div>
    <div class="absolute inset-0 bg-black/60 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60 pointer-events-none"></div>
    <div class="relative w-full h-full md:h-[90vh] md:max-w-md mx-auto overflow-hidden md:rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border-x border-white/5 bg-black flex flex-col items-center justify-center group overflow-hidden"
         onmousedown="pauseStory()" onmouseup="resumeStory()" onmouseleave="resumeStory()"
         ontouchstart="pauseStory()" ontouchend="resumeStory()">
        <!-- Main Content Area (Background Layer) -->
        <div id="storyContent" class="absolute inset-0 w-full h-full flex items-center justify-center bg-black">
            <!-- Loading Spinner -->
            <div id="storyLoading" class="absolute inset-0 z-[65] flex items-center justify-center bg-black">
                <div class="w-10 h-10 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
            </div>

            <img id="storyViewerImg" class="relative z-10 w-full h-full object-contain hidden" onerror="handleImageError(this)">
            <div id="storyVideoWrapper" class="w-full h-full hidden"></div>
            <div id="storyTextOverlay" class="w-full h-full flex items-center justify-center p-12 text-center text-white font-black text-3xl leading-snug hidden"></div>
            
            <!-- Mentions Interaction Layer -->
            <div id="storyMentionsLayer" class="absolute inset-0 z-[61] pointer-events-none overflow-hidden"></div>
        </div>

        <!-- Progress Bars (Top Layer) -->
        <div class="absolute top-0 w-full px-4 pt-6 pb-2 space-x-1.5 z-[70] flex" id="storyBars"></div>

        <!-- Header (Top Layer) -->
        <div class="absolute top-10 w-full px-4 flex items-center justify-between z-[70]">
            <div id="storyProfileContainer" class="flex items-center space-x-3 cursor-pointer pointer-events-auto active:opacity-80 transition-opacity">
                <img id="storyUserAvatar" class="w-10 h-10 rounded-full border-2 border-white/20 shadow-xl object-cover bg-bg-tertiary" onerror="handleImageError(this)">
                <div class="flex flex-col">
                    <p id="storyUsername" class="text-white font-black text-[12px] leading-tight tracking-tight italic uppercase drop-shadow-lg"></p>
                    <p id="storyTime" class="text-white/80 text-[9px] font-black tracking-widest uppercase drop-shadow-md"></p>
                </div>
            </div>
            <button onclick="closeStoryViewer()" class="p-2.5 bg-black/40 hover:bg-black/60 rounded-2xl backdrop-blur-md transition-all active:scale-90 border border-white/10 group-hover:border-white/20 shadow-lg pointer-events-auto">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <!-- Navigation Tap Zones (Interaction Layer) -->
        <div class="absolute inset-y-0 left-0 w-1/4 z-[60] cursor-pointer" onclick="previousStory()"></div>
        <div class="absolute inset-y-0 right-0 w-1/4 z-[60] cursor-pointer" onclick="nextStory()"></div>

        <!-- Story Footer (Interactive) -->
        <div class="absolute bottom-6 left-0 right-0 px-4 z-[70]">
            
            <!-- Viewer Controls (For Others) -->
            <div id="storyViewerControls" class="flex items-center space-x-3 hidden">
                <div class="flex-1 relative">
                    <input type="text" id="storyReplyInput" placeholder="Send message..." 
                           class="w-full bg-transparent border border-white/30 rounded-full px-5 py-3 text-white placeholder-white/70 backdrop-blur-md focus:border-white focus:bg-white/10 transition-all outline-none text-sm pointer-events-auto"
                           onkeypress="if(event.key === 'Enter') sendStoryReply(this)">
                </div>
                <button onclick="likeStory()" id="storyLikeBtn" class="p-3 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white hover:bg-white/20 active:scale-90 transition-all pointer-events-auto">
                    <svg id="storyLikeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                <button onclick="openShareStoryModal()" class="p-3 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white hover:bg-white/20 active:scale-90 transition-all pointer-events-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>


            <!-- Owner Stats (For You) -->
            <div id="storyOwnerStats" class="flex flex-col items-center space-y-4 hidden pb-4">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 bg-black/40 backdrop-blur-xl px-4 py-2 rounded-full border border-white/10 text-white text-xs font-bold pointer-events-auto cursor-pointer hover:bg-black/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span id="storyViewCount">0 Views</span>
                    </div>
                    <button onclick="openShareStoryModal()" class="p-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white hover:bg-white/20 active:scale-90 transition-all pointer-events-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Story Modal -->
<div id="shareStoryModal" class="fixed inset-0 z-[105] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-bg-secondary rounded-3xl w-full max-w-md max-h-[80vh] flex flex-col shadow-2xl border border-border-light anim-scale-in">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-border-light">
            <h3 class="text-lg font-bold text-text-primary">Share Story</h3>
            <button onclick="closeShareStoryModal()" class="p-2 hover:bg-bg-tertiary rounded-full transition-colors">
                <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Search -->
        <div class="p-4 border-b border-border-light">
            <div class="relative">
                <input type="text" id="shareSearchInput" placeholder="Search friends..." 
                       onkeyup="filterShareUsers(this.value)"
                       class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-2 pl-10 text-sm text-text-primary placeholder-text-tertiary focus:border-primary-500 outline-none transition-colors">
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-text-tertiary pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <!-- Users List -->
        <div id="shareUsersList" class="flex-1 overflow-y-auto p-4 space-y-2">
            <!-- Loading -->
            <div id="shareUsersLoading" class="flex items-center justify-center py-8">
                <div class="w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <!-- Users will be injected here -->
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-border-light">
            <textarea id="shareMessageInput" placeholder="Add a message (optional)..." 
                      class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-2 text-sm text-text-primary placeholder-text-tertiary resize-none focus:border-primary-500 outline-none transition-colors" 
                      rows="2"></textarea>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-border-light flex justify-between items-center">
            <span id="selectedUsersCount" class="text-sm text-text-secondary">0 selected</span>
            <button onclick="sendSharedStory()" id="shareStoryBtn" disabled
                    class="px-6 py-2 bg-primary-500 text-white rounded-full font-bold hover:bg-primary-400 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                Send
            </button>
        </div>
    </div>
</div>

<style>
    .anim-scale-in { animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes slide-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in-up {
            animation: slide-in-up 0.3s ease-out forwards;
        }
    
    @keyframes float-up {
        0% { transform: translateY(0) scale(0.5); opacity: 0; }
        15% { opacity: 1; transform: translateY(-50px) scale(1.2); }
        80% { opacity: 0.8; }
        100% { transform: translateY(-300px) scale(1); opacity: 0; }
    }
    .animate-float-up { animation: float-up 2.5s ease-out forwards; }
</style>


<!-- STORY EDITOR OVERLAY -->
<div id="storyEditor" class="fixed inset-0 z-[110] bg-black hidden flex-col anim-fade-in touch-none">
    <!-- Top Bar -->
    <div class="absolute top-0 w-full p-4 flex justify-between items-center z-[120] bg-gradient-to-b from-black/60 to-transparent">
        <button onclick="closeStoryEditor()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="flex space-x-4">
            <!-- Undo Button (Hidden by default, shown when drawing) -->
            <button onclick="undoDrawing()" id="editorUndoBtn" class="hidden p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </button>

            <button onclick="toggleBrushTool()" id="editorBrushBtn" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button onclick="toggleTextTool()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
            <button onclick="toggleStickerSheet()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button onclick="toggleMentionSheet()" id="editorMentionBtn" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                <span class="font-bold text-lg">@</span>
            </button>
            
            <!-- Text Style Controls (Grouped) -->
            <div id="textControls" class="hidden flex space-x-2">
                <button onclick="cycleFonts()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                    <span id="currentFontIcon" class="font-bold text-sm">Aa</span>
                </button>
                <button onclick="toggleTextStyle()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors">
                     <span class="font-bold text-lg">A</span>
                </button>
            </div>

            <button onclick="cycleFilters()" class="p-2 rounded-full bg-black/20 backdrop-blur text-white hover:bg-white/10 transition-colors relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span id="filterNameBadge" class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 text-[10px] bg-black/50 text-white px-2 py-0.5 rounded-full hidden whitespace-nowrap">Normal</span>
            </button>
        </div>
    </div>

    <!-- Canvas Area -->
    <div class="relative flex-1 bg-[#1a1a1a] flex items-center justify-center overflow-hidden" id="editorCanvasContainer">
        <img id="editorPreview" class="max-w-full max-h-full object-contain transition-all duration-300" src="">
        <canvas id="drawingLayer" class="absolute inset-0 pointer-events-none touch-none"></canvas>
        
        <input type="text" id="editorCaptionInput" 
               class="absolute hidden bg-black/50 text-white text-center font-bold text-xl p-2 rounded-lg backdrop-blur-md border-none focus:ring-0 outline-none w-3/4"
               placeholder="Type something..."
               onblur="finalizeCaption()"
               onkeydown="event.stopPropagation()"
               onkeypress="event.stopPropagation()"
               style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div id="editorCaptionDisplay" 
             class="draggable-item absolute hidden px-4 py-2 rounded-xl backdrop-blur-sm text-white font-bold text-lg cursor-move select-none"
             style="top: 50%; left: 50%; transform: translate(-50%, -50%); touch-action: none;"
             onmousedown="dragStart(event)" ontouchstart="dragStart(event)"></div>
    </div>

    <!-- Sticker Sheet -->
    <div id="stickerSheet" class="absolute bottom-24 left-4 right-4 bg-black/80 backdrop-blur-xl rounded-2xl p-4 grid grid-cols-5 gap-4 text-3xl hidden z-[130] overflow-y-auto max-h-60 anim-slide-up">
        <div onclick="addSticker('🔥')" class="cursor-pointer hover:scale-125 transition-transform text-center">🔥</div>
        <div onclick="addSticker('❤️')" class="cursor-pointer hover:scale-125 transition-transform text-center">❤️</div>
        <div onclick="addSticker('😍')" class="cursor-pointer hover:scale-125 transition-transform text-center">😍</div>
        <div onclick="addSticker('😂')" class="cursor-pointer hover:scale-125 transition-transform text-center">😂</div>
        <div onclick="addSticker('😮')" class="cursor-pointer hover:scale-125 transition-transform text-center">😮</div>
        <div onclick="addSticker('🎉')" class="cursor-pointer hover:scale-125 transition-transform text-center">🎉</div>
        <div onclick="addSticker('💯')" class="cursor-pointer hover:scale-125 transition-transform text-center">💯</div>
        <div onclick="addSticker('📍')" class="cursor-pointer hover:scale-125 transition-transform text-center">📍</div>
        <div onclick="addSticker('✨')" class="cursor-pointer hover:scale-125 transition-transform text-center">✨</div>
        <div onclick="addSticker('⏰')" class="cursor-pointer hover:scale-125 transition-transform text-center">⏰</div>
    </div>

    <!-- Color Palette (Hidden by default) -->
    <div id="editorColorPalette" class="absolute bottom-24 left-0 right-0 flex justify-center space-x-3 z-[130] hidden anim-slide-up">
        <!-- Colors injected by JS -->
    </div>

    <!-- Mention Sheet -->
    <div id="mentionSheet" class="absolute bottom-24 left-4 right-4 bg-black/80 backdrop-blur-xl rounded-2xl hidden z-[130] anim-slide-up max-h-80 flex flex-col">
        <!-- Search -->
        <div class="p-3 border-b border-white/10">
            <input type="text" id="mentionSearchInput" placeholder="Search to mention..." 
                   onkeyup="filterMentionUsers(this.value)"
                   class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-sm text-white placeholder-white/60 focus:border-primary-500 outline-none">
        </div>
        
        <!-- Users List -->
        <div id="mentionUsersList" class="flex-1 overflow-y-auto p-2">
            <!-- Loading -->
            <div id="mentionUsersLoading" class="flex items-center justify-center py-8">
                <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
     <div class="w-full p-6 pb-8 bg-gradient-to-t from-black via-black/80 to-transparent flex justify-between items-end z-[120]">
        <div class="flex flex-col space-y-2">
           <button class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full text-white text-xs font-bold border border-white/10 flex items-center space-x-2">
               <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
               <span>Story Mode</span>
           </button>
        </div>

        <button onclick="publishStory()" class="flex items-center space-x-2 bg-primary-500 hover:bg-primary-400 text-white font-black px-6 py-3 rounded-full shadow-lg shadow-primary-500/20 transform active:scale-95 transition-all">
            <span>Your Story</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </div>
</div>

<!-- LIVE VIEWER OVERLAY -->
<div id="liveViewer" class="fixed inset-0 z-[120] bg-black hidden flex-col items-center justify-center anim-fade-in touch-none">
    <div class="relative w-full h-full md:h-[90vh] md:max-w-md mx-auto overflow-hidden md:rounded-[3rem] shadow-2xl bg-black flex flex-col group">
        <!-- Video Layer -->
        <div id="liveVideoContainer" class="absolute inset-0 w-full h-full bg-black flex items-center justify-center">
            <video id="remoteLiveVideo" class="w-full h-full object-cover" autoplay playsinline muted></video>
            <div id="liveLoading" class="absolute inset-0 flex items-center justify-center bg-black/60 z-10 transition-opacity duration-300">
                <div class="w-12 h-12 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
            </div>
            <div id="liveEndedOverlay" class="absolute inset-0 flex flex-col items-center justify-center bg-black/90 z-40 hidden anim-fade-in">
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-white text-xl font-black mb-1">Live Ended</h3>
                <p class="text-white/60 text-sm mb-6">This broadcast has concluded</p>
                <button onclick="closeLiveViewer()" class="px-8 py-3 bg-white text-black font-black rounded-full hover:bg-white/90 transition-all active:scale-95">Go Back</button>
            </div>
        </div>

        <!-- Header -->
        <div class="absolute top-8 w-full px-4 flex items-center justify-between z-20">
            <div class="flex items-center space-x-3 bg-black/20 backdrop-blur-md p-1.5 pr-4 rounded-full border border-white/10 cursor-pointer active:scale-95 transition-transform" 
                 onclick="const name = document.getElementById('liveHostName').innerText.trim(); if(name) window.location.href='/profile/' + name">
                <img id="liveHostAvatar" class="w-8 h-8 rounded-full border border-white/20 object-cover" src="">
                <div class="flex flex-col">
                    <span id="liveHostName" class="text-white text-[11px] font-black leading-tight">Host Name</span>
                    <div class="flex items-center space-x-1.5">
                        <span class="bg-red-500 text-white text-[8px] font-black px-1 rounded-sm uppercase italic">Live</span>
                        <div class="flex items-center space-x-1 text-white/90 text-[10px]">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                            <span id="uniqueLiveViewerCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <button onclick="closeLiveViewer()" class="p-2.5 bg-black/40 hover:bg-black/60 rounded-2xl backdrop-blur-md transition-all active:scale-90 border border-white/10">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Comments Area (Overlay) -->
        <div class="absolute bottom-24 left-0 right-0 max-h-[40%] overflow-y-auto px-4 flex flex-col-reverse z-20 no-scrollbar pointer-events-none" id="liveCommentsList">
            <!-- Comments injected here -->
        </div>

        <!-- Input Area -->
        <div class="absolute bottom-6 left-0 right-0 px-4 flex items-center space-x-3 z-30">
            <div class="flex-1 relative">
                <input type="text" id="liveCommentInput" placeholder="Comment..." 
                       class="w-full bg-black/30 border border-white/20 rounded-full pl-5 pr-12 py-3 text-white placeholder-white/60 backdrop-blur-md focus:border-white focus:bg-white/10 transition-all outline-none text-sm shadow-xl"
                       onkeypress="if(event.key === 'Enter') sendLiveComment()">
                <button onclick="sendLiveComment()" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-white/60 hover:text-primary-500 transition-colors active:scale-90">
                    <svg class="w-5 h-5 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
            <button onclick="sendLiveReaction('heart')" class="group p-3 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white hover:bg-white/20 active:scale-90 transition-all shadow-xl">
                <svg class="w-6 h-6 group-active:text-red-500 group-active:fill-current transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
        </div>

        <!-- Floating Reactions Container -->
        <div id="reactionContainer" class="absolute bottom-24 right-4 w-20 h-64 pointer-events-none z-10"></div>
    </div>
</div>

<!-- LIVE HOST OVERLAY -->
<div id="liveHost" class="fixed inset-0 z-[120] bg-black hidden flex-col items-center justify-center anim-fade-in touch-none">
    <div class="relative w-full h-full md:h-[90vh] md:max-w-md mx-auto overflow-hidden md:rounded-[3rem] shadow-2xl bg-black flex flex-col group">
        <video id="localLiveVideo" class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline></video>
        
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60 pointer-events-none"></div>

        <div id="hostEndedOverlay" class="absolute inset-0 flex flex-col items-center justify-center bg-black/95 z-50 hidden anim-fade-in backdrop-blur-xl">
            <div class="w-20 h-20 bg-primary-500/20 rounded-full flex items-center justify-center mb-6 border border-primary-500/30">
                <svg class="w-10 h-10 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </div>
            <h3 class="text-white text-2xl font-black mb-2 uppercase tracking-widest">Broadcast Finished</h3>
            <p class="text-white/50 text-sm mb-12 font-medium">Your live session was successful!</p>
            <button onclick="location.reload()" class="px-12 py-4 bg-primary-500 text-white font-black rounded-2xl shadow-2xl shadow-primary-500/40 hover:bg-primary-400 transition-all active:scale-95 uppercase tracking-tighter">Done</button>
        </div>

        <div class="absolute top-8 w-full px-4 flex items-center justify-between z-20">
            <div class="flex items-center space-x-2 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-sm uppercase italic">
                Live
            </div>
            <div class="flex items-center space-x-3">
                <div id="hostViewerCountContainer" class="px-3 py-1.5 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-white text-[10px] font-bold">
                    <span id="uniqueHostViewerCount">0</span> viewers
                </div>
                <div class="px-3 py-1.5 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-white text-[10px] font-bold flex items-center space-x-1">
                    <svg class="w-3 h-3 text-red-500 fill-current" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                    <span id="hostLikeCount">0</span>
                </div>
                <button onclick="console.log('End Live button clicked'); endLive()" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-tighter rounded-full transition-colors active:scale-95">
                    End Live
                </button>
            </div>
        </div>

        <div class="absolute bottom-24 left-0 right-0 max-h-[30%] overflow-y-auto px-4 flex flex-col-reverse z-20 no-scrollbar pointer-events-none" id="hostCommentsList"></div>

        <div class="absolute bottom-8 left-0 right-0 flex justify-center z-30 space-x-4">
             <button onclick="switchCamera()" class="p-4 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
            <button onclick="toggleMic()" id="micBtn" class="p-4 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
            </button>
            <!-- Dev Settings Button -->
            <button onclick="openDevSettings()" class="p-4 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white opacity-40 hover:opacity-100 transition-opacity">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
        </div>

        <!-- DEV SETTINGS MODAL -->
        <div id="devSettingsModal" class="absolute inset-0 bg-black/90 z-50 hidden flex flex-col items-center justify-center p-8 anim-fade-in text-white text-center">
            <h3 class="text-xl font-black mb-4 uppercase tracking-widest text-primary-500">Developer Options</h3>
            <p class="text-[10px] text-white/50 mb-6 px-4 uppercase tracking-tighter">Use these tools to bypass HTTPS restrictions on iPhone/Safari tunnels.</p>
            
            <div class="w-full space-y-4 mb-8">
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-black text-white/40 block text-left pl-2">Signaling URL (8080)</label>
                    <input type="text" id="devSignalingUrl" placeholder="wss://your-tunnel.loca.lt" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs focus:border-primary-500 transition-all outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-black text-white/40 block text-left pl-2">SFU URL (8888)</label>
                    <input type="text" id="devSfuUrl" placeholder="https://your-sfu-tunnel.loca.lt" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs focus:border-primary-500 transition-all outline-none">
                </div>
            </div>

            <div class="flex flex-col w-full space-y-3">
                <button onclick="saveDevSettings()" class="w-full py-4 bg-primary-500 text-white font-black rounded-2xl shadow-xl shadow-primary-500/20 active:scale-95 transition-all">Apply & Reload</button>
                <button onclick="closeDevSettings()" class="w-full py-3 bg-white/5 text-white/60 text-xs font-bold rounded-2xl hover:bg-white/10 transition-all">Close</button>
                <button onclick="localStorage.clear(); location.reload();" class="text-[10px] text-red-500/50 uppercase font-black mt-4 hover:text-red-500 transition-colors">Reset All Settings</button>
            </div>
        </div>
    </div>
</div>

<!-- Core Logic -->
    // DEBUG PANEL
    <div id="signalingDebug" class="fixed top-24 right-4 z-[9999] bg-black/80 backdrop-blur text-white text-[10px] p-2 rounded border border-white/20 font-mono hidden pointer-events-none">
        <div>STATUS: <span id="debugStatus" class="font-bold text-yellow-500">INIT</span></div>
        <div>LIVE ID: <span id="debugLiveId">-</span></div>
        <div>ROLE: <span id="debugRole">-</span></div>
        <div>AUDIENCE: <span id="debugCount">-</span></div>
        <div id="debugLastMsg" class="text-white/50 truncate max-w-[150px]">-</div>
        <button onclick="forceSync()" class="mt-2 text-primary-500 font-bold hover:underline">[FORCE SYNC]</button>
        <button onclick="testUI()" class="mt-2 text-red-500 font-bold hover:underline ml-2">[TEST UI]</button>
    </div>
    
<script>
    // Auto-refresh loop to heal any dropped connections
    setInterval(() => {
        if (liveHostInstance?.room?.ws?.readyState === WebSocket.OPEN) {
             liveHostInstance.room.send('TRIGGER_POPULATION_REFRESH', {});
        }
        if (liveViewerInstance?.room?.ws?.readyState === WebSocket.OPEN) {
             liveViewerInstance.room.send('TRIGGER_POPULATION_REFRESH', {});
        }
    }, 4000);
    // Enable debug panel with checking URL param ?debug=true
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('debug') === 'true') {
        setTimeout(() => document.getElementById('signalingDebug')?.classList.remove('hidden'), 1000);
    }

    function updateDebug(key, value, color) {
        const el = document.getElementById('debug' + key);
        if (el) {
            el.innerText = value;
            if (color) el.className = `font-bold text-${color}-500`;
        }
    }

    function forceSync() {
        if (liveHostInstance?.room) liveHostInstance.room.send('TRIGGER_POPULATION_REFRESH', {});
        if (liveViewerInstance?.room) liveViewerInstance.room.send('TRIGGER_POPULATION_REFRESH', {});
        window.toast('Sync signal sent', 'info');
    }

    function testUI() {
        const h = document.getElementById('uniqueHostViewerCount');
        const v = document.getElementById('uniqueLiveViewerCount');
        if(h) { h.innerText = '999'; h.style.color='red'; }
        if(v) { v.innerText = '999'; v.style.color='red'; }
        window.toast('UI Forced to 999', 'warning');
    }
    window.onerror = function(msg, url, lineNo, columnNo, error) {
        console.error('GLOBAL ERROR:', msg, error);
        window.toast('JS Error: ' + msg, 'error');
        return false;
    };
    
    let feedPosts = {};    // Global map for post data
    let feedPlayers = {};  // Global map for video players
    let viewTimers = {};   // View counting logic
    
    // --- LIVE SYSTEM STATE ---
    let currentLiveRoom = null;
    let liveHostInstance = null;
    let liveViewerInstance = null;

    class LiveRoom {
        constructor(liveId, token, signalingUrl, iceServers) {
            this.liveId = liveId;
            this.token = token;
            this.signalingUrl = signalingUrl;
            this.iceServers = iceServers;
            this.ws = null;
            this.onMessage = null;
            updateDebug('LiveId', liveId);
        }

        connect() {
            return new Promise((resolve, reject) => {
                this.ws = new WebSocket(this.signalingUrl);
                this.ws.onopen = () => {
                    updateDebug('Status', 'CONNECTED', 'green');
                    this.send('AUTH', { token: this.token });
                };
                this.ws.onmessage = (e) => {
                    const data = JSON.parse(e.data);
                    document.getElementById('debugLastMsg').innerText = data.type;
                    if (data.type === 'AUTH_OK') {
                        updateDebug('Role', data.payload.role);
                        resolve(data.payload);
                    }
                    if (data.type === 'ERROR') {
                        updateDebug('Status', 'ERROR', 'red');
                        reject(data.payload);
                    }
                    if (this.onMessage) this.onMessage(data);
                };
                this.ws.onerror = (e) => {
                    console.error('WebSocket Error:', e);
                    reject(new Error('Signaling server unreachable at ' + this.signalingUrl));
                };
            });
        }

        send(type, payload) {
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                const msg = { type, payload: { ...payload, liveId: this.liveId } };
                console.log(`[LiveRoom SEND]`, type, msg.payload);
                this.ws.send(JSON.stringify(msg));
            } else {
                console.warn(`[LiveRoom SEND FAILED] WS not open. State: ${this.ws?.readyState}`);
            }
        }

        close() {
            if (this.ws) this.ws.close();
        }
    }

    class LiveHost {
        constructor(liveId, token, signalingUrl, iceServers) {
            this.room = new LiveRoom(liveId, token, signalingUrl, iceServers);
            this.pc = null;
            this.localStream = null;
            this.likeCount = 0;
        }

        async start() {
            document.getElementById('liveHost')?.classList.remove('hidden');
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Camera access requires HTTPS or Localhost (Secure Context)');
                }
                
                // Mobile-optimized constraints
                const constraints = {
                    video: {
                        width: { ideal: 720 },
                        height: { ideal: 1280 }
                    },
                    audio: true
                };

                this.localStream = await navigator.mediaDevices.getUserMedia(constraints)
                    .catch(err => {
                        console.error('Initial getUserMedia failed, trying fallback:', err);
                        return navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                    });
                
                document.getElementById('localLiveVideo').srcObject = this.localStream;
                
                this.setupRealtime();
                console.log('Connecting to Signaling...');
                await this.room.connect().catch(err => {
                    throw new Error('Signaling server unreachable. Make sure "npm run dev" is running in signaling-gateway.');
                });
                
                this.pc = new RTCPeerConnection({ iceServers: this.room.iceServers });

                this.pc.oniceconnectionstatechange = () => {
                    console.log('Host ICE Connection State:', this.pc.iceConnectionState);
                };

                this.localStream.getTracks().forEach(track => this.pc.addTrack(track, this.localStream));

                this.pc.onicecandidate = (e) => {
                    if (e.candidate) this.room.send('ICE_CANDIDATE', { candidate: e.candidate });
                };

                const offer = await this.pc.createOffer();
                await this.pc.setLocalDescription(offer);

                const response = await fetch(`${config.sfuUrl}/publish`, {
                    method: 'POST',
                    body: JSON.stringify({ sdp: offer.sdp, roomId: String(this.room.liveId) })
                }).catch(err => {
                    console.error('SFU Error:', err);
                    throw new Error('SFU unreachable at ' + window.config.sfuUrl);
                });
                
                const { sdp } = await response.json();
                await this.pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp }));

                this.setupRealtime();
            } catch (e) {
                console.error('Host failed:', e);
                window.toast(e.message, 'error');
                this.stop();
            }
        }

        setupRealtime() {
            this.room.onMessage = (data) => {
                console.log('[Host Signaling]', data.type, data.payload);
                if (data.type === 'ROOM_POPULATION') {
                    console.log('[Host] Population received:', data.payload.count);
                    updateDebug('Count', data.payload.count + ' (Total: ' + data.payload.total + ')');
                    const el = document.getElementById('uniqueHostViewerCount');
                    if (el) {
                        el.innerText = data.payload.count;
                        document.getElementById('hostViewerCountContainer')?.classList.remove('hidden');
                    }
                }
                if (data.type === 'CHAT_MESSAGE') {
                    this.addComment(data.payload.comment);
                }
                if (data.type === 'REACTION') {
                    this.popReaction(data.payload.reaction);
                }
                if (data.type === 'ICE_CANDIDATE') {
                    if (this.pc) this.pc.addIceCandidate(new RTCIceCandidate(data.payload.candidate));
                }
            };
        }

        addComment(comment) {
            console.log('[Host] Adding comment to UI:', comment);
            const list = document.getElementById('hostCommentsList');
            if (!list) return;
            
            const el = document.createElement('div');
            el.className = 'flex items-start space-x-2 mb-3 animate-slide-in-up pointer-events-auto';
            
            const avatar = comment.avatar || `https://ui-avatars.com/api/?name=${comment.username}&background=random`;
            
            el.innerHTML = `
                <img src="${avatar}" class="w-8 h-8 rounded-full border border-white/20 object-cover shadow-sm bg-black/20 shrink-0">
                <div class="flex flex-col">
                    <span class="text-white font-bold text-xs drop-shadow-md cursor-pointer hover:underline" onclick="window.location.href='/profile/${encodeURIComponent(comment.username)}'">${comment.username}</span>
                    <span class="text-white/90 text-[13px] drop-shadow-md leading-tight">${comment.message}</span>
                </div>
            `;
            list.prepend(el);
        }

        popReaction(type) {
            if (typeof showReactionPopup === 'function') {
                showReactionPopup(type);
            }
        }

        async switchCamera() {
            if (!this.localStream) return;
            const videoTrack = this.localStream.getVideoTracks()[0];
            const currentFacingMode = videoTrack.getSettings().facingMode;
            const newFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
            
            try {
                const newStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: newFacingMode },
                    audio: true 
                });
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                const sender = this.pc.getSenders().find(s => s.track && s.track.kind === 'video');
                if (sender) sender.replaceTrack(newVideoTrack);
                
                videoTrack.stop();
                this.localStream.removeTrack(videoTrack);
                this.localStream.addTrack(newVideoTrack);
                document.getElementById('localLiveVideo').srcObject = this.localStream;
            } catch (e) {
                console.error('Camera switch failed:', e);
            }
        }

        toggleMic() {
            if (!this.localStream) return;
            const audioTrack = this.localStream.getAudioTracks()[0];
            audioTrack.enabled = !audioTrack.enabled;
            const btn = document.getElementById('micBtn');
            if (audioTrack.enabled) {
                btn.classList.remove('bg-red-500');
                btn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>`;
                window.toast('Microphone On', 'info');
            } else {
                btn.classList.add('bg-red-500');
                btn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" /></svg>`;
                window.toast('Microphone Muted', 'warning');
            }
        }

        stop() {
            if (this.pc) this.pc.close();
            if (this.localStream) this.localStream.getTracks().forEach(t => t.stop());
            this.room.close();
            document.getElementById('hostEndedOverlay').classList.remove('hidden');
        }
    }

    class LiveViewer {
        constructor(liveId, token, signalingUrl, iceServers) {
            this.room = new LiveRoom(liveId, token, signalingUrl, iceServers);
            this.pc = null;
        }

        async join() {
            document.getElementById('liveViewer')?.classList.remove('hidden');
            document.getElementById('liveLoading')?.classList.remove('opacity-0');
            try {
                this.room.onMessage = (data) => {
                    console.log('[Viewer Signaling]', data.type, data.payload);
                    if (data.type === 'ICE_CANDIDATE') {
                        if (this.pc) this.pc.addIceCandidate(new RTCIceCandidate(data.payload.candidate)).catch(e => console.error('ICE Error', e));
                    }
                    if (data.type === 'LIVE_ENDED') {
                        this.showEndedOverlay();
                    }
                    if (data.type === 'ROOM_POPULATION') {
                        console.log('[Viewer] Population received:', data.payload.count);
                        updateDebug('Count', data.payload.count);
                        const el = document.getElementById('uniqueLiveViewerCount');
                        if (el) el.innerText = data.payload.count;
                    }
                    if (data.type === 'CHAT_MESSAGE') {
                        this.addComment(data.payload.comment);
                    }
                    if (data.type === 'REACTION') {
                        this.popReaction(data.payload.reaction);
                    }
                };

                console.log('Connecting to Signaling (Viewer)...');
                await this.room.connect().catch(err => {
                    throw new Error('Signaling server unreachable. Make sure "npm run dev" is running in signaling-gateway.');
                });
                
                this.pc = new RTCPeerConnection({ iceServers: this.room.iceServers });

                this.pc.oniceconnectionstatechange = () => {
                    console.log('ICE Connection State:', this.pc.iceConnectionState);
                    if (this.pc.iceConnectionState === 'failed') {
                        window.toast('Media connection failed. Try Refresh.', 'error');
                    }
                };

                this.pc.ontrack = (e) => {
                    console.log('Received remote track:', e.track.kind, e.streams[0]?.id);
                    const videoEl = document.getElementById('remoteLiveVideo');
                    if (videoEl.srcObject !== e.streams[0]) {
                        videoEl.srcObject = e.streams[0];
                    }
                    document.getElementById('liveLoading')?.classList.add('opacity-0');
                    videoEl.play().catch(err => {
                        console.warn('Autoplay prevented, showing play button...', err);
                    });
                };

                this.pc.onicecandidate = (e) => {
                    if (e.candidate) {
                        console.log('New ICE candidate:', e.candidate.candidate);
                        this.room.send('ICE_CANDIDATE', { candidate: e.candidate });
                    }
                };

                this.pc.addTransceiver('video', { direction: 'recvonly' });
                this.pc.addTransceiver('audio', { direction: 'recvonly' });

                const offer = await this.pc.createOffer();
                await this.pc.setLocalDescription(offer);

                const response = await fetch(`${config.sfuUrl}/subscribe`, {
                    method: 'POST',
                    body: JSON.stringify({ 
                        sdp: offer.sdp, 
                        roomId: String(this.room.liveId),
                        userId: String(window.currentUser.id)
                    })
                }).catch(err => {
                    console.error('SFU Error:', err);
                    throw new Error('SFU unreachable at ' + window.config.sfuUrl);
                });
                
                const { sdp } = await response.json();
                await this.pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp }));

                console.log('Viewer live setup complete!');
            } catch (e) {
                console.error('Viewer failed:', e);
                window.toast(e.message || 'Live connection failed', 'error');
                this.leave();
            }
        }

        setupRealtime() {
            // Echo removed. Everything handled via signaling.onMessage in join()
        }

        addComment(comment) {
            console.log('[Viewer] Adding comment to UI:', comment);
            const list = document.getElementById('liveCommentsList');
            if (!list) return;
            
            const el = document.createElement('div');
            el.className = 'flex items-start space-x-2 mb-3 animate-slide-in-up pointer-events-auto';
            
            const avatar = comment.avatar || `https://ui-avatars.com/api/?name=${comment.username}&background=random`;
            
            el.innerHTML = `
                <img src="${avatar}" class="w-8 h-8 rounded-full border border-white/20 object-cover shadow-sm bg-black/20 shrink-0">
                <div class="flex flex-col">
                    <span class="text-white font-bold text-xs drop-shadow-md cursor-pointer hover:underline" onclick="window.location.href='/profile/${encodeURIComponent(comment.username)}'">${comment.username}</span>
                    <span class="text-white/90 text-[13px] drop-shadow-md leading-tight">${comment.message}</span>
                </div>
            `;
            list.prepend(el);
        }

        popReaction(type) {
            if (typeof showReactionPopup === 'function') {
                showReactionPopup(type);
            }
        }

        showEndedOverlay() {
            window.toast('لقد انتهى البث المباشر', 'info');
            document.getElementById('liveEndedOverlay').classList.remove('hidden');
            if (this.pc) {
                this.pc.close();
                this.pc = null;
            }
            this.room.close();
        }

        leave() {
            if (this.pc) this.pc.close();
            this.room.close();
            document.getElementById('liveViewer')?.classList.add('hidden');
            document.getElementById('liveEndedOverlay')?.classList.add('hidden');
            document.getElementById('liveLoading')?.classList.remove('opacity-0');
        }
    }

    // --- LIVE ACTIONS ---
    async function startLive() {
        try {
            const data = await window.bridge.request('/live/start', { method: 'POST' });
            const sigUrl = localStorage.getItem('SIGNALING_URL') || data.wsSignalingUrl;
            liveHostInstance = new LiveHost(data.liveId, data.token, sigUrl, data.iceServers);
            
            // Global config for classes
            let sfuUrl = localStorage.getItem('SFU_URL') || sigUrl.replace('ws', 'http').replace('8080', '8888');
            window.config = { sfuUrl }; 
            await liveHostInstance.start();
        } catch (err) {
            window.toast(err.error || err.message || 'Failed to start live', 'error');
        }
    }

    async function openLiveViewer(liveId) {
        console.log('Attempting to join live:', liveId);
        if (!liveId) {
            window.toast('Error: Missing Live ID', 'error');
            return;
        }
        try {
            const data = await window.bridge.request(`/live/${liveId}/join`, { method: 'POST' });
            console.log('Join response data:', data);
            const sigUrl = localStorage.getItem('SIGNALING_URL') || data.wsSignalingUrl;
            liveViewerInstance = new LiveViewer(data.liveId, data.token, sigUrl, data.iceServers);
            
            let sfuUrl = localStorage.getItem('SFU_URL') || sigUrl.replace('ws', 'http').replace('8080', '8888');
            window.config = { sfuUrl };
            
            // Set host UI info
            const liveInfo = await window.bridge.request(`/live/${liveId}`);
            const host = liveInfo.host;
            console.log('Live host info received:', host);
            
            const avatarEl = document.getElementById('liveHostAvatar');
            const defaultAvatar = 'https://ui-avatars.com/api/?name=' + host.username + '&background=random';
            avatarEl.src = host.avatar_url || defaultAvatar;
            avatarEl.onerror = () => { avatarEl.src = defaultAvatar; };
            
            const nameEl = document.getElementById('liveHostName');
            nameEl.innerText = host.username;
            
            // Fix navigation link
            const profileHeader = avatarEl.closest('.cursor-pointer');
            if (profileHeader) {
                profileHeader.onclick = () => window.location.href = `/profile/${host.username.trim()}`;
            }
            
            await liveViewerInstance.join();
        } catch (err) {
            console.error('Join Error:', err);
            window.toast(err.error || err.message || 'Failed to join live', 'error');
        }
    }

    function closeLiveViewer() {
        if (liveViewerInstance) liveViewerInstance.leave();
        liveViewerInstance = null;
    }

    async function endLive() {
        console.log('End Live initiated');
        // alert('End Live Button Pressed'); // DEBUG FOR MOBILE
        if (!liveHostInstance) {
            console.error('No live host instance found');
            window.toast('No active live session found', 'warning');
            return;
        }
        
        const liveId = liveHostInstance.room.liveId;
        window.toast('Ending broadcast...', 'info');

        try {
            // 1. Notify backend
            await window.bridge.request(`/live/${liveId}/end`, { method: 'POST' })
                .catch(err => console.warn('Backend end-session failed, proceeding anyway:', err));

            // 2. Notify viewers via signaling
            try {
                liveHostInstance.room.send('HOST_END_LIVE', {});
            } catch (e) {
                console.warn('Signaling end-live failed:', e);
            }

            // 3. Stop local resources
            liveHostInstance.stop();
            liveHostInstance = null;
            
            window.toast('Live ended successfully', 'success');
            if (typeof loadStories === 'function') loadStories();
        } catch (err) {
            console.error('End live critical failure:', err);
            window.toast('Failed to end live. Please refresh page.', 'error');
        }
    }

    function switchCamera() {
        if (liveHostInstance) liveHostInstance.switchCamera();
    }

    function toggleMic() {
        if (liveHostInstance) liveHostInstance.toggleMic();
    }

    // Dev settings
    // AUTO-CONFIG VIA URL (Magic Link)
    (function() {
        const params = new URLSearchParams(window.location.search);
        const sig = params.get('signaling');
        const sfu = params.get('sfu');
        if (sig && sfu) {
            localStorage.setItem('SIGNALING_URL', sig);
            localStorage.setItem('SFU_URL', sfu);
            // Delay toast slightly to ensure UI is ready
            setTimeout(() => window.toast('Configuration loaded from link! 🚀', 'success'), 1000);
        }
    })();

    function openDevSettings() {
        document.getElementById('devSignalingUrl').value = localStorage.getItem('SIGNALING_URL') || '';
        document.getElementById('devSfuUrl').value = localStorage.getItem('SFU_URL') || '';
        document.getElementById('devSettingsModal').classList.remove('hidden');
    }

    function closeDevSettings() {
        document.getElementById('devSettingsModal').classList.add('hidden');
    }

    function saveDevSettings() {
        const sig = document.getElementById('devSignalingUrl').value.trim();
        const sfu = document.getElementById('devSfuUrl').value.trim();
        
        if (sig) localStorage.setItem('SIGNALING_URL', sig.replace('http', 'ws'));
        if (sfu) localStorage.setItem('SFU_URL', sfu);
        
        window.toast('Settings applied!', 'success');
        setTimeout(() => location.reload(), 500);
    }

    window.openDevSettings = openDevSettings;
    window.closeDevSettings = closeDevSettings;
    window.saveDevSettings = saveDevSettings;

    // Expose actions to global scope just in case
    window.startLive = startLive;
    window.endLive = endLive;
    window.openLiveViewer = openLiveViewer;
    window.closeLiveViewer = closeLiveViewer;
    window.switchCamera = switchCamera;
    window.toggleMic = toggleMic;
    window.sendLiveComment = sendLiveComment;
    window.sendLiveReaction = sendLiveReaction;

    async function sendLiveComment() {
        const input = document.getElementById('liveCommentInput');
        const liveId = liveViewerInstance?.room.liveId || liveHostInstance?.room.liveId;
        if (!input.value.trim() || !liveId) return;

        try {
            const res = await window.bridge.request(`/live/${liveId}/comment`, {
                method: 'POST',
                body: JSON.stringify({ message: input.value })
            });

            // Instant delivery via Signaling
            const commentData = {
                username: window.currentUser.username,
                avatar: window.currentUser.avatar_url, // Pass avatar
                message: input.value,
                created_at: new Date().toISOString()
            };
            
            const room = liveViewerInstance?.room || liveHostInstance?.room;
            if (room) {
                console.log('[Comment] Sending via signaling...', commentData);
                room.send('CHAT_MESSAGE', { comment: commentData });
            }

            // Local add for sender
            if (liveViewerInstance) liveViewerInstance.addComment(commentData);
            if (liveHostInstance) liveHostInstance.addComment(commentData);

            input.value = '';
        } catch (err) {
            console.error('Comment failed', err);
        }
    }

    async function sendLiveReaction(reaction) {
        const liveId = liveViewerInstance?.room.liveId || liveHostInstance?.room.liveId;
        if (!liveId) return;
        
        // Local pop
        showReactionPopup(reaction);
        
        try {
            // Persistent storage
            await window.bridge.request(`/live/${liveId}/react`, {
                method: 'POST',
                body: JSON.stringify({ reaction })
            });

            // Instant relay via Signaling
            const room = liveViewerInstance?.room || liveHostInstance?.room;
            if (room) {
                room.send('REACTION', { reaction });
            }
        } catch (err) {
            console.error('Reaction failed', err);
        }
    }

    function showReactionPopup(type) {
        const container = document.getElementById('reactionContainer');
        const icon = document.createElement('div');
        const emojis = {
            'heart': ['❤️', '💖', '💝', '✨'],
            'fire': ['🔥', '💥', '⚡', '✨']
        };
        const list = emojis[type] || ['✨'];
        const randomEmoji = list[Math.floor(Math.random() * list.length)];
        
        icon.className = 'absolute bottom-0 right-0 text-2xl animate-float-up opacity-0 pointer-events-none z-50';
        icon.innerText = randomEmoji;
        icon.style.right = Math.random() * 40 + 'px';
        icon.style.filter = `hue-rotate(${Math.random() * 360}deg)`;
        container.appendChild(icon);
        setTimeout(() => icon.remove(), 2500);
    }
    let nextCursor = null; // Pagination
    let isLoading = false; // Prevent double loads
    let lastFeedTap = 0;   // Double tap to like
    let loadingTimeout = null;
    let touchStartX = 0;   
    let touchStartY = 0;   
    let allGroupedStories = [];
    let currentUserGroupIndex = 0;
    let storyIndex = 0;
    let currentStories = [];
    let isPaused = false;
    let longPressTimer = null;
    let progressStartTime = 0;
    let remainingTime = 5000;
    let storyTimer = null;

    // API Integration
    async function loadStories() {
        const rail = document.getElementById('storiesRail');
        if (rail) {
            // Skeleton Loading State
            rail.innerHTML = Array(5).fill(0).map(() => `
                <div class="flex-shrink-0 flex flex-col items-center space-y-1 animate-pulse">
                    <div class="w-20 h-20 rounded-full bg-white/5 border-2 border-white/10"></div>
                    <div class="h-2 w-12 bg-white/10 rounded"></div>
                </div>
            `).join('');
        }

        try {
            console.log('Loading stories...');
            const data = await window.bridge.request('/stories');
            allGroupedStories = Array.isArray(data.stories) ? data.stories : (data.stories ? Object.values(data.stories) : []);
            renderStories();
        } catch (err) {
            console.error('Stories failed:', err);
            // Even if it fails, ensure the rail is functional for adding (clears skeleton)
            renderStories();
        }
    }

    function renderStories() {
        const rail = document.getElementById('storiesRail');
        if (!rail) return;

        // Clear rail but keep the input if it was accidentally inside (though we moved it out)
        // Check if input exists outside. If not, we might have lost it? 
        // No, we replaced the block in Blade, so it's in the DOM.
        
        rail.innerHTML = '';
        

        // 2. Process "You" (Current User)
        const myUsername = window.currentUser?.username;
        const myGroupIndex = allGroupedStories.findIndex(g => g && g.user && g.user.username === myUsername);
        
        let myStories = [];
        let hasMyStories = false;
        
        if (myGroupIndex !== -1) {
            const group = allGroupedStories[myGroupIndex];
            if (group.type !== 'live') {
                const rawStories = group.stories || [];
                myStories = Array.isArray(rawStories) ? rawStories : Object.values(rawStories);
                hasMyStories = myStories.length > 0;
            }
        }

        const myAvatar = window.currentUser?.avatar_url || `https://ui-avatars.com/api/?name=${window.currentUser?.username || 'You'}`;
        const myItem = document.createElement('div');
        myItem.className = 'flex-shrink-0 flex flex-col items-center space-y-1 cursor-pointer group';
        
        const borderClass = hasMyStories 
            ? 'bg-gradient-to-tr from-primary-500 to-accent-500 p-0.5' 
            : 'border-2 border-dashed border-border-light p-0.5 hover:border-primary-500 transition-colors';

        myItem.innerHTML = `
            <div class="relative w-20 h-20 transition-transform group-hover:scale-105">
                <div class="w-full h-full rounded-full ${borderClass} flex items-center justify-center">
                    <img src="${myAvatar}" class="w-full h-full rounded-full border-2 border-bg-primary object-cover" onerror="handleImageError(this)">
                </div>
                <div class="absolute bottom-0 -right-0 w-7 h-7 bg-primary-500 rounded-full text-white flex items-center justify-center border-2 border-bg-primary cursor-pointer hover:scale-110 transition-transform shadow-md"
                     onclick="event.stopPropagation(); openCreateContentModal();" title="Add Story">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
            <span class="text-[10px] text-text-secondary font-medium w-20 truncate text-center">You</span>
        `;

        myItem.onclick = () => {
            if (hasMyStories) {
                openStoryViewer(myStories); // If stories exist, view them
            } else {
                openCreateContentModal(); // If no stories, offer to create (Live/Story)
            }
        };
        
        rail.appendChild(myItem);

        // 2. Render Others
        if (!allGroupedStories) return;

        allGroupedStories.forEach((group, index) => {
            if (!group || !group.user) return;
            
            const { user, type } = group;
            if (user.username === myUsername && type !== 'live') return;

            const item = document.createElement('div');
            item.className = 'flex-shrink-0 flex flex-col items-center space-y-1 cursor-pointer group';
            
            if (type === 'live') {
                item.onclick = () => openLiveViewer(group.live_id);
                item.innerHTML = `
                    <div class="relative w-20 h-20 transition-transform group-hover:scale-105">
                        <div class="w-full h-full rounded-full bg-gradient-to-tr from-red-500 to-pink-500 p-0.5 flex items-center justify-center">
                            <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" class="w-full h-full rounded-full border-2 border-bg-primary object-cover" onerror="handleImageError(this)">
                        </div>
                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-red-500 text-white text-[9px] font-black px-2 py-0.5 rounded-sm border border-bg-primary uppercase tracking-tighter shadow-md">Live</div>
                    </div>
                    <span class="text-[10px] text-text-secondary font-medium w-20 truncate text-center">${user.username}</span>
                `;
            } else {
                let stories = group.stories || [];
                stories = Array.isArray(stories) ? stories : Object.values(stories);
                if (stories.length === 0) return;

                item.onclick = () => {
                    currentUserGroupIndex = index;
                    openStoryViewer(stories);
                };

                const borderClass = group.has_unviewed 
                    ? 'bg-gradient-to-tr from-primary-500 to-accent-500' 
                    : 'bg-gray-400';

                item.innerHTML = `
                    <div class="w-20 h-20 rounded-full ${borderClass} p-0.5 transition-transform group-hover:scale-105">
                        <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" class="w-full h-full rounded-full border-2 border-bg-primary object-cover" onerror="handleImageError(this)">
                    </div>
                    <span class="text-[10px] text-text-secondary font-medium w-20 truncate text-center">${user.username}</span>
                `;
            }
            rail.appendChild(item);
        });
    }


    function openStoryViewer(stories) {
        // Ensure stories is an array
        currentStories = Array.isArray(stories) ? stories : Object.values(stories);
        if (!currentStories || currentStories.length === 0) return;
        
        // Find first unviewed if exists
        let firstUnviewedIndex = currentStories.findIndex(s => !s.views || s.views.length === 0);
        storyIndex = firstUnviewedIndex !== -1 ? firstUnviewedIndex : 0;
        
        isPaused = false;
        const viewer = document.getElementById('storyViewer');
        if (viewer) viewer.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Lock scrolling
        showStory();
    }

    function closeStoryViewer() {
        document.getElementById('storyViewer').classList.add('hidden');
        document.body.style.overflow = ''; 
        clearTimeout(storyTimer);
        isPaused = false;
        if (window.storyVideoPlayer) {
            window.storyVideoPlayer.dispose();
            window.storyVideoPlayer = null;
        }
        // Refresh rail to reorder watched stories
        loadStories();
    }

    function showStory() {
        if (!currentStories || storyIndex < 0 || storyIndex >= currentStories.length) {
            closeStoryViewer();
            return;
        }

        const story = currentStories[storyIndex];
        const viewerImg = document.getElementById('storyViewerImg');
        const textOverlay = document.getElementById('storyTextOverlay');
        const videoWrapper = document.getElementById('storyVideoWrapper');
        const contentArea = document.getElementById('storyContent');
        const loading = document.getElementById('storyLoading');
        const backdrop = document.getElementById('storyBackdrop');
        
        if (!viewerImg || !textOverlay || !videoWrapper || !contentArea || !loading || !backdrop) return;

        // Reset state
        viewerImg.classList.add('hidden');
        textOverlay.classList.add('hidden');
        videoWrapper.classList.add('hidden');
        loading.classList.remove('hidden'); 
        videoWrapper.innerHTML = ''; 
        contentArea.style.background = 'black'; 
        backdrop.style.backgroundImage = 'none';

        if (window.storyVideoPlayer) {
            window.storyVideoPlayer.dispose();
            window.storyVideoPlayer = null;
        }

        clearTimeout(storyTimer);
        clearTimeout(loadingTimeout);

        // Safety timeout: hide loader after 8s anyway
        loadingTimeout = setTimeout(() => {
            loading.classList.add('hidden');
        }, 8000);

        // UI Header
        const user = story.user || { username: 'Neural User', avatar_url: '' };
        const avatarImg = document.getElementById('storyUserAvatar');
        const nameEl = document.getElementById('storyUsername');
        const timeEl = document.getElementById('storyTime');

        if (avatarImg) avatarImg.src = user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.username)}&background=2D3FE6&color=fff`;
        if (nameEl) nameEl.innerText = user.display_name || user.username;
        if (timeEl) timeEl.innerText = story.created_at_human || 'Just now';

        // Profile Click Navigation
        const profileContainer = document.getElementById('storyProfileContainer');
        if (profileContainer) {
            profileContainer.onclick = (e) => {
                e.stopPropagation(); // Prevent closing/skipping
                window.location.href = `/profile/${user.username}`;
            };
        }

        // Progress Bars
        const bars = document.getElementById('storyBars');
        if (bars) {
            bars.innerHTML = currentStories.map((_, i) => `
                <div class="flex-1 h-1 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white transition-none" style="width: ${i < storyIndex ? '100%' : '0%'}"></div>
                </div>
            `).join('');
        }

        // Handle Content Type
        if (story.type === 'video') {
            videoWrapper.classList.remove('hidden');
            backdrop.style.backgroundImage = story.thumbnail_url ? `url(${story.thumbnail_url})` : 'none';
            window.storyVideoPlayer = new VideoPlayer(videoWrapper, story.media_url, {
                autoplay: true,
                muted: false,
                loop: false,
                isReel: true,
                onCanPlay: () => {
                    clearTimeout(loadingTimeout);
                    loading.classList.add('hidden');
                },
                onPlaying: () => {
                    clearTimeout(loadingTimeout);
                    loading.classList.add('hidden');
                },
                onTimeUpdate: (percentage) => {
                    const bars = document.getElementById('storyBars')?.children;
                    if (bars && bars[storyIndex]) {
                        const progressEl = bars[storyIndex].firstElementChild;
                        if (progressEl) {
                            progressEl.style.transition = 'none'; // Direct update
                            progressEl.style.width = `${percentage}%`;
                        }
                    }
                },
                onEnded: () => nextStory(),
                onError: () => {
                    clearTimeout(loadingTimeout);
                    nextStory();
                }
            });
        } else if (story.type === 'text') {
            clearTimeout(loadingTimeout);
            loading.classList.add('hidden'); 
            contentArea.style.background = story.background_color || '#2D3FE6';
            backdrop.style.backgroundImage = 'none';
            textOverlay.innerText = story.text_content;
            textOverlay.classList.remove('hidden');
            remainingTime = 5000;
            startStoryTimer();
        } else {
            backdrop.style.backgroundImage = `url(${story.media_url})`;
            viewerImg.src = story.media_url;
            viewerImg.classList.remove('hidden');
            viewerImg.onload = () => {
                clearTimeout(loadingTimeout);
                loading.classList.add('hidden');
                remainingTime = 5000;
                startStoryTimer();
                
                // RENDER MENTIONS
                renderStoryMentions(story, viewerImg, contentArea);
            };
            viewerImg.onerror = () => {
                clearTimeout(loadingTimeout);
                loading.classList.add('hidden');
                nextStory();
            };
        }
        
        window.bridge.request(`/stories/${story.id}/view`, { method: 'POST' }).catch(() => {});

        // --- INTERACTIVE FOOTER LOGIC ---
        const isOwner = story.user.username === (window.currentUser?.username || '');
        const controls = document.getElementById('storyViewerControls');
        const stats = document.getElementById('storyOwnerStats');
        const likeIcon = document.getElementById('storyLikeIcon');
        
        if (isOwner) {
             controls.classList.add('hidden');
             stats.classList.remove('hidden');
             document.getElementById('storyViewCount').innerText = `${story.views_count || 0} Views`;
        } else {
             stats.classList.add('hidden');
             controls.classList.remove('hidden');
             
             // Update Like State
             const isLiked = story.is_liked || false;
             if (isLiked) {
                 likeIcon.classList.add('text-red-500', 'fill-current');
             } else {
                 likeIcon.classList.remove('text-red-500', 'fill-current');
             }
        }
        
        // Setup mention click handlers
        setupStoryMentionHandlers();
    }

    function renderStoryMentions(story, img, container) {
        const layer = document.getElementById('storyMentionsLayer');
        if (!layer) return;
        layer.innerHTML = '';
        
        console.log('[Mentions] Data:', story.mentions);
        if (!story.mentions || !Array.isArray(story.mentions) || story.mentions.length === 0) {
            console.log('[Mentions] No metadata found for this story.');
            return;
        }
        
        // Wait for next tick to ensure container size is correct
        setTimeout(() => {
            const imgRatio = img.naturalWidth / img.naturalHeight;
            const contRect = container.getBoundingClientRect();
            const contRatio = contRect.width / contRect.height;
            
            console.log('[Mentions] Container Rect:', contRect.width, 'x', contRect.height);
            
            let renderW, renderH, offsetX, offsetY;
            
            if (contRatio > imgRatio) { // Pillarbox
                renderH = contRect.height;
                renderW = renderH * imgRatio;
                offsetX = (contRect.width - renderW) / 2;
                offsetY = 0;
            } else { // Letterbox
                renderW = contRect.width;
                renderH = renderW / imgRatio;
                offsetX = 0;
                offsetY = (contRect.height - renderH) / 2;
            }

            console.log('[Mentions] Render Metrics:', { renderW, renderH, offsetX, offsetY });

            story.mentions.forEach(mention => {
                console.log('[Mentions] Rendering tag for:', mention.username, 'at', mention.x, mention.y);
                const el = document.createElement('div');
                el.className = 'absolute pointer-events-auto cursor-pointer group rounded-lg transition-all hover:bg-white/10 active:scale-95';
                
                const x = (mention.x / 100) * renderW + offsetX;
                const y = (mention.y / 100) * renderH + offsetY;
                
                el.style.left = x + 'px';
                el.style.top = y + 'px';
                el.style.transform = 'translate(-50%, -50%)';
                el.style.width = '120px'; // Slightly larger targets
                el.style.height = '60px';
                
                // Add a very subtle outline while debugging
                el.style.border = '1px dashed rgba(255,255,255,0.1)';
                
                el.onclick = (e) => {
                    console.log('[Mentions] Clicked:', mention.username);
                    e.stopPropagation();
                    pauseStory();
                    window.location.href = `/profile/${mention.username}`;
                };
                
                layer.appendChild(el);
            });
        }, 100); // 100ms for safety
    }
    
    function setupStoryMentionHandlers() {
        // Add click handlers to all mention tags in the story
        const storyContent = document.getElementById('currentStoryContent');
        if (!storyContent) return;
        
        // For image/video stories, mentions should be overlays
        // For now, we'll handle this in the viewer display
        // This will be called after story loads
    }

    // --- INTERACTION FUNCTIONS ---
    async function likeStory() {
        if (!currentStories[storyIndex]) return;
        const story = currentStories[storyIndex];
        const btn = document.getElementById('storyLikeBtn');
        const icon = document.getElementById('storyLikeIcon');
        
        // Optimistic UI
        const isLiked = icon.classList.contains('text-red-500');
        if (isLiked) {
            icon.classList.remove('text-red-500', 'fill-current');
            // story.is_liked = false; 
        } else {
            icon.classList.add('text-red-500', 'fill-current');
            // Animation
            icon.style.transform = 'scale(1.4)';
            setTimeout(() => icon.style.transform = 'scale(1)', 200);
            // story.is_liked = true;
        }

        try {
            await window.bridge.request(`/stories/${story.id}/like`, { method: 'POST' });
        } catch (err) {
            console.error('Like failed', err);
        }
    }

    async function sendStoryReply(input) {
        if (!input.value.trim() || !currentStories[storyIndex]) return;
        const msg = input.value;
        const storyId = currentStories[storyIndex].id;
        
        input.value = '';
        input.blur();
        
        // Show local feedback
        window.toast('Sent!', 'success');
        
        try {
            await window.bridge.request(`/stories/${storyId}/reply`, { 
                method: 'POST',
                body: JSON.stringify({ message: msg })
            });
        } catch (err) {
            console.error('Reply failed', err);
            window.toast('Failed to send', 'error');
        }
    }

    function startStoryTimer(isResume = false) {
        clearTimeout(storyTimer);
        const bars = document.getElementById('storyBars')?.children;
        const progress = bars && bars[storyIndex] ? bars[storyIndex].firstElementChild : null;

        if (!isResume) {
            // New start - always reset visual state
            if (progress) {
                progress.style.transition = 'none';
                progress.style.width = '0%';
                progress.getBoundingClientRect(); // Trigger reflow
            }
        }
        
        if (isPaused) {
            // If paused, we don't start the timer or animation
            return;
        }

        if (progress) {
            progress.style.transition = `width ${remainingTime}ms linear`;
            setTimeout(() => {
                if (!isPaused) progress.style.width = '100%';
            }, 50);
        }
        
        storyTimer = setTimeout(() => nextStory(true), remainingTime);
        progressStartTime = Date.now();
    }

    let pauseStartTime = 0;
    let preventClick = false;

    function pauseStory() {
        if (isPaused) return;
        isPaused = true;
        clearTimeout(storyTimer);
        
        pauseStartTime = Date.now();
        preventClick = false;

        // Calculate remaining time
        const elapsed = Date.now() - progressStartTime;
        remainingTime -= elapsed;
        if (remainingTime < 0) remainingTime = 0;

        // Freeze progress bar
        const bars = document.getElementById('storyBars')?.children;
        if (bars && bars[storyIndex]) {
            const progress = bars[storyIndex].firstElementChild;
            const computedStyle = window.getComputedStyle(progress);
            const w = computedStyle.getPropertyValue('width');
            progress.style.transition = 'none';
            progress.style.width = w;
        }

        // Pause video if applicable
        if (window.storyVideoPlayer) {
            window.storyVideoPlayer.pause();
        }
    }

    function resumeStory() {
        if (!isPaused) return;
        isPaused = false;
        
        // Prevent click if held for more than 200ms
        if (Date.now() - pauseStartTime > 200) {
            preventClick = true;
            setTimeout(() => preventClick = false, 300);
        }

        // Resume video if applicable
        if (window.storyVideoPlayer) {
            window.storyVideoPlayer.play();
        } else {
            // Resume text/image timer
            startStoryTimer(true);
        }
    }

    function nextStory(fromTimer = false) {
        // Only block if it's a click and we are preventing clicks (long press release)
        if (!fromTimer && typeof fromTimer !== 'boolean' && preventClick) return;
        
        if (storyIndex < currentStories.length - 1) {
            storyIndex++;
            remainingTime = 5000;
            showStory();
        } else {
            if (currentUserGroupIndex < allGroupedStories.length - 1) {
                currentUserGroupIndex++;
                currentStories = Array.isArray(allGroupedStories[currentUserGroupIndex].stories) 
                    ? allGroupedStories[currentUserGroupIndex].stories 
                    : Object.values(allGroupedStories[currentUserGroupIndex].stories);
                storyIndex = 0;
                remainingTime = 5000;
                showStory();
            } else {
                closeStoryViewer();
            }
        }
    }

    function previousStory() {
        if (preventClick) return;
        if (storyIndex > 0) {
            storyIndex--;
            remainingTime = 5000;
            showStory();
        } else {
            if (currentUserGroupIndex > 0) {
                currentUserGroupIndex--;
                currentStories = Array.isArray(allGroupedStories[currentUserGroupIndex].stories) 
                    ? allGroupedStories[currentUserGroupIndex].stories 
                    : Object.values(allGroupedStories[currentUserGroupIndex].stories);
                storyIndex = currentStories.length - 1;
                remainingTime = 5000;
                showStory();
            } else {
                storyIndex = 0;
                remainingTime = 5000;
                showStory();
            }
        }
    }

    function createVideoContainer() {
        const div = document.createElement('div');
        div.id = 'storyVideoPlayer';
        div.className = 'w-full h-full';
        document.getElementById('storyViewerImg').parentElement.appendChild(div);
        return div;
    }

    function createTextBlob(story) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(story.text_content)}&background=${story.background_color.replace('#','')}&color=fff&size=512`;
    }

    // Gesture Logic
    let storyViewer = document.getElementById('storyViewer');
    storyViewer.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        longPressTimer = setTimeout(() => pauseStory(), 200);
    });

    storyViewer.addEventListener('touchend', e => {
        clearTimeout(longPressTimer);
        if (isPaused) {
            resumeStory();
            return;
        }

        const deltaX = e.changedTouches[0].clientX - touchStartX;
        const deltaY = e.changedTouches[0].clientY - touchStartY;

        if (Math.abs(deltaY) > 100 && deltaY > 0) {
            closeStoryViewer();
            return;
        }

        if (Math.abs(deltaX) < 10) {
            const width = window.innerWidth;
            if (touchStartX < width / 3) previousStory();
            else nextStory();
        }
    });

    function pauseStory() {
        isPaused = true;
        clearTimeout(storyTimer);
        const bars = document.getElementById('storyBars').children;
        if (bars[storyIndex]) {
            const progress = bars[storyIndex].firstElementChild;
            const computedWidth = getComputedStyle(progress).width;
            progress.style.transition = 'none';
            progress.style.width = computedWidth;
        }
        if (window.storyVideoPlayer) window.storyVideoPlayer.pause();
    }

    function resumeStory() {
        isPaused = false;
        const elapsed = Date.now() - progressStartTime;
        remainingTime = Math.max(0, remainingTime - elapsed);
        if (currentStories[storyIndex].type !== 'video') {
            startStoryTimer();
        }
        if (window.storyVideoPlayer) window.storyVideoPlayer.play();
    }

    // --- STORY EDITOR LOGIC ---
    let currentFile = null;
    
    // Editor State
    let isDragging = false;
    let dragOffsetX = 0, dragOffsetY = 0;
    let isDrawing = false;
    let drawingMode = false;
    let brushColor = '#ffffff';
    let textColor = '#ffffff';
    let textBgMode = 0; // 0: None, 1: Translucent, 2: Solid
    
    const colors = ['#ffffff', '#000000', '#ff0000', '#ffff00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#orange'];

    // Drawing Canvas State
    let drawCtx;
    let lastX = 0;
    let lastY = 0;

    async function uploadStory(input) {
        if (!input.files || !input.files[0]) return;
        currentFile = input.files[0];
        
        // Reset editor state
        resetEditor();

        // Load preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('editorPreview');
            img.src = e.target.result;
            
            // Wait for image to load to size canvas
            img.onload = () => {
                const canvas = document.getElementById('drawingLayer');
                const container = document.getElementById('editorCanvasContainer');
                canvas.width = container.clientWidth;
                canvas.height = container.clientHeight;
                drawCtx = canvas.getContext('2d');
                drawCtx.lineCap = 'round';
                drawCtx.lineJoin = 'round';
                drawCtx.lineWidth = 5;
            };

            document.getElementById('storyEditor').classList.remove('hidden');
            document.getElementById('storyEditor').classList.add('flex');
            setupPalette();
        };
        reader.readAsDataURL(currentFile);
        
        input.value = ''; 
    }

    function resetEditor() {
        document.getElementById('editorCaptionInput').value = '';
        document.getElementById('editorCaptionInput').classList.add('hidden');
        document.getElementById('editorCaptionDisplay').innerText = '';
        document.getElementById('editorCaptionDisplay').classList.add('hidden');
        document.getElementById('editorPreview').style.filter = '';
        document.getElementById('editorColorPalette').classList.add('hidden');
        
        // Clear Canvas
        const canvas = document.getElementById('drawingLayer');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        currentFilterIndex = 0;
        isDrawing = false;
        drawingMode = false;
        textBgMode = 0;
        brushColor = '#ffffff';
        textColor = '#ffffff';
        updateTextStyle();
    }

    function setupPalette() {
        const p = document.getElementById('editorColorPalette');
        p.innerHTML = '';
        colors.forEach(c => {
            const btn = document.createElement('div');
            btn.className = `w-8 h-8 rounded-full border-2 border-white cursor-pointer hover:scale-110 transition-transform shadow-md`;
            btn.style.backgroundColor = c;
            btn.onclick = () => setColor(c);
            p.appendChild(btn);
        });
    }

    function setColor(c) {
        if (drawingMode) {
             brushColor = c;
             drawCtx.strokeStyle = c;
        } else {
             // Assume Text Mode if not drawing
             // Only update text color if text tool is conceptually "active" or just always update text state?
             // User wants them separate. If I'm drawing, I shouldn't change text color.
             // If I'm NOT drawing, I'm likely editing text or preparing to.
             textColor = c;
             updateTextStyle(); 
        }
        
        // Visual feedback on palette? (Optional)
    }

    function toggleBrushTool() {
        drawingMode = !drawingMode;
        const canvas = document.getElementById('drawingLayer');
        const palette = document.getElementById('editorColorPalette');
        const btn = document.getElementById('editorBrushBtn');
        const stickerSheet = document.getElementById('stickerSheet');
        const textControls = document.getElementById('textControls');

        if (stickerSheet) stickerSheet.classList.add('hidden');
        if (textControls) textControls.classList.add('hidden');

        if (drawingMode) {
            if (canvas) canvas.classList.remove('pointer-events-none');
            if (palette) palette.classList.remove('hidden');
            if (btn) {
                btn.classList.add('bg-primary-500', 'text-white');
                btn.classList.remove('bg-black/20');
            }
            
            // Close text
            const input = document.getElementById('editorCaptionInput');
            if (input) input.classList.add('hidden');
        } else {
            if (canvas) canvas.classList.add('pointer-events-none');
            if (palette) palette.classList.add('hidden');
            if (btn) {
                btn.classList.remove('bg-primary-500', 'text-white');
                btn.classList.add('bg-black/20');
            }
        }
    }

    function toggleTextTool() {
        const input = document.getElementById('editorCaptionInput');
        const display = document.getElementById('editorCaptionDisplay');
        const palette = document.getElementById('editorColorPalette');
        const textControls = document.getElementById('textControls');
        const stickerSheet = document.getElementById('stickerSheet');
        
        if (stickerSheet) stickerSheet.classList.add('hidden');
        
        // Disable drawing if enabling text
        if (drawingMode) toggleBrushTool();

        if (display && display.innerText.trim() === '') {
            if (input) {
                input.classList.remove('hidden');
                input.focus();
            }
            if (palette) palette.classList.remove('hidden');
            if (textControls) textControls.classList.remove('hidden');
        } else {
            if (input && !input.classList.contains('hidden')) {
                finalizeCaption();
                if (palette) palette.classList.add('hidden');
                if (textControls) textControls.classList.add('hidden');
            } else {
                if (input && display) {
                    input.value = display.innerText;
                    input.classList.remove('hidden');
                    display.classList.add('hidden');
                    input.focus();
                }
                if (palette) palette.classList.remove('hidden');
                if (textControls) textControls.classList.remove('hidden');
            }
        }
    }
    
    function toggleTextStyle() {
        textBgMode = (textBgMode + 1) % 3;
        updateTextStyle();
    }

    function updateTextStyle() {
        // Apply to Display and Input
        const els = [document.getElementById('editorCaptionDisplay'), document.getElementById('editorCaptionInput')];
        
        els.forEach(el => {
            el.style.color = textColor;
            
            if (textBgMode === 0) {
                el.style.backgroundColor = 'transparent';
                el.style.textShadow = '0 2px 4px rgba(0,0,0,0.8)'; // Shadow for visibility
                el.classList.remove('text-black');
            } else if (textBgMode === 1) {
                el.style.backgroundColor = 'rgba(0,0,0,0.5)';
                el.style.textShadow = 'none';
                el.classList.remove('text-black');
            } else {
                el.style.backgroundColor = textColor === '#ffffff' ? '#ffffff' : textColor;
                el.style.textShadow = 'none';
                // Inverse text color if background is bright
                // Simple heuristic: if solid color, make text white or black?
                // Actually usually "Solid" mode makes the BOX the selected color and text Black/White.
                // Let's keep it simple: Solid box = Selected Color, Text = White/Black constrast.
                el.style.color = (textColor === '#ffffff' || textColor === '#ffff00' || textColor === '#00ffff' || textColor === '#orange') ? 'black' : 'white';
            }
        });
    }

    // --- FILTER LOGIC ---
    const filters = [
        { name: 'Normal', val: '' },
        { name: 'Vivid', val: 'saturate(1.5) contrast(1.1)' },
        { name: 'B&W', val: 'grayscale(1)' },
        { name: 'Warm', val: 'sepia(0.4) saturate(1.2)' },
        { name: 'Cool', val: 'hue-rotate(180deg) saturate(0.8)' },
        { name: 'Vintage', val: 'sepia(0.6) contrast(1.2) brightness(0.9)' },
        { name: 'Cyber', val: 'hue-rotate(45deg) contrast(1.2) saturate(2)' }
    ];
    let currentFilterIndex = 0;

    function cycleFilters() {
        currentFilterIndex = (currentFilterIndex + 1) % filters.length;
        const f = filters[currentFilterIndex];
        document.getElementById('editorPreview').style.filter = f.val;
        
        // Show Badge
        const badge = document.getElementById('filterNameBadge');
        if(badge) {
            badge.innerText = f.name;
            badge.classList.remove('hidden');
            clearTimeout(window.filterBadgeTimer);
            window.filterBadgeTimer = setTimeout(() => badge.classList.add('hidden'), 1500);
        }
    }

    // --- FONT LOGIC ---
    const fonts = ['sans-serif', 'serif', 'monospace', 'cursive', 'fantasy'];
    const fontNames = ['Classic', 'Serif', 'Mono', 'Hand', 'Neon'];
    let currentFontIndex = 0;

    function cycleFonts() {
        currentFontIndex = (currentFontIndex + 1) % fonts.length;
        const font = fonts[currentFontIndex];
        const display = document.getElementById('editorCaptionDisplay');
        const input = document.getElementById('editorCaptionInput');
        const icon = document.getElementById('currentFontIcon');
        
        display.style.fontFamily = font;
        input.style.fontFamily = font;
        if(icon) icon.innerText = fontNames[currentFontIndex].substring(0, 2); // Abbrev
        
        window.toast(`Font: ${fontNames[currentFontIndex]}`, 'success');
    }

    // --- DRAWING LOGIC WITH UNDO ---
    let drawHistory = [];
    const MAX_HISTORY = 20;

    function saveDrawState() {
        const canvas = document.getElementById('drawingLayer');
        if (!drawCtx) return;
        if (drawHistory.length >= MAX_HISTORY) drawHistory.shift();
        drawHistory.push(drawCtx.getImageData(0, 0, canvas.width, canvas.height));
        const btn = document.getElementById('editorUndoBtn');
        if(btn) btn.classList.remove('hidden');
    }

    function undoDrawing() {
        if (drawHistory.length === 0) return;
        const canvas = document.getElementById('drawingLayer');
        const lastState = drawHistory.pop();
        
        if (lastState) {
            drawCtx.putImageData(lastState, 0, 0);
        } else {
            // Clear if empty
            drawCtx.clearRect(0, 0, canvas.width, canvas.height);
        }
        
        if (drawHistory.length === 0) {
             const btn = document.getElementById('editorUndoBtn');
             if(btn) btn.classList.add('hidden');
        }
    }

    // DRAWING EVENTS
    const canvas = document.getElementById('drawingLayer');
    
    document.addEventListener('DOMContentLoaded', () => {
         const c = document.getElementById('drawingLayer');
         if(c) {
            c.width = c.parentElement?.clientWidth || window.innerWidth;
            c.height = c.parentElement?.clientHeight || window.innerHeight;
            drawCtx = c.getContext('2d');
            drawCtx.lineCap = 'round';
            drawCtx.lineJoin = 'round';
            drawCtx.lineWidth = 5;
         }
    });

    // Mouse
    if(canvas) {
        canvas.addEventListener('mousedown', startDrawWrapper);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseout', stopDraw);
        
        // Touch
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            startDrawWrapper(e.touches[0]);
        }, {passive: false});
        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            draw(e.touches[0]);
        }, {passive: false});
        canvas.addEventListener('touchend', stopDraw);
    }
    
    function startDrawWrapper(e) {
        if (!drawingMode) return;
        // Don't prevent default here for mouse, might break selection? 
        // Actually we want to draw.
        
        saveDrawState(); // Save BEFORE drawing
        startDraw(e);
    }

    function startDraw(e) {
        isDrawing = true;
        const rect = document.getElementById('drawingLayer').getBoundingClientRect();
        lastX = e.clientX - rect.left;
        lastY = e.clientY - rect.top;
    }

    function draw(e) {
        if (!isDrawing || !drawingMode) return;
        const rect = document.getElementById('drawingLayer').getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        drawCtx.beginPath();
        drawCtx.strokeStyle = brushColor;
        drawCtx.moveTo(lastX, lastY);
        drawCtx.lineTo(x, y);
        drawCtx.stroke();
        
        lastX = x;
        lastY = y;
    }

    function stopDraw() {
        isDrawing = false;
    }

    function closeStoryEditor() {
        document.getElementById('storyEditor').classList.add('hidden');
        document.getElementById('storyEditor').classList.remove('flex');
        currentFile = null;
    }


    function finalizeCaption() {
        const input = document.getElementById('editorCaptionInput');
        const display = document.getElementById('editorCaptionDisplay');
        
        if (input.value.trim()) {
            display.innerText = input.value;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
        input.classList.add('hidden');
    }

    // --- GENERIC DRAG LOGIC ---
    let activeDragEl = null;

    function dragStart(e) {
        // Find draggable target
        const target = e.target.closest('.draggable-item');
        if (!target) return;
        
        isDragging = true;
        activeDragEl = target;
        
        // e.preventDefault(); // Don't prevent default immediately, might block inputs? No, text input is separate.
        // Actually for touch, preventDefault stops scrolling.
    }

    function dragMove(e) {
        if (!isDragging || !activeDragEl) return;
        e.preventDefault(); 
        
        const container = document.getElementById('editorCanvasContainer');
        const containerRect = container.getBoundingClientRect();
        
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        // Calculate relative position within container
        let x = clientX - containerRect.left;
        let y = clientY - containerRect.top;
        
        // Center the element on the finger/mouse
        // We set left/top. Since transform is -50%, x/y is the center.
        
        // Constraints (Allow slight over-drag to hide edges?)
        // x = Math.max(0, Math.min(x, containerRect.width));
        // y = Math.max(0, Math.min(y, containerRect.height));
        
        activeDragEl.style.left = x + 'px';
        activeDragEl.style.top = y + 'px';
    }

    function dragEnd() {
        isDragging = false;
        activeDragEl = null;
    }
    
    // Add global drag listeners to container
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('editorCanvasContainer');
        if (container) {
            container.addEventListener('mousemove', dragMove);
            container.addEventListener('mouseup', dragEnd);
            container.addEventListener('mouseleave', dragEnd);
            container.addEventListener('touchmove', dragMove, { passive: false });
            container.addEventListener('touchend', dragEnd);
        }
    });
    
    // --- STICKER LOGIC ---
    function toggleStickerSheet() {
        const sheet = document.getElementById('stickerSheet');
        const isHidden = sheet.classList.contains('hidden');
        document.getElementById('editorColorPalette').classList.add('hidden'); // Hide color
        
        if (isHidden) {
            sheet.classList.remove('hidden');
        } else {
            sheet.classList.add('hidden');
        }
    }

    function addSticker(emoji) {
        const container = document.getElementById('editorCanvasContainer');
        const el = document.createElement('div');
        el.className = 'draggable-item absolute text-5xl cursor-move select-none transition-transform active:scale-125';
        el.innerText = emoji;
        el.style.left = '50%';
        el.style.top = '50%';
        el.style.transform = 'translate(-50%, -50%)';
        el.style.touchAction = 'none'; // Critical for dragging
        
        // Add events
        el.onmousedown = dragStart;
        el.ontouchstart = dragStart;
        
        container.appendChild(el);
        toggleStickerSheet();
    }

    // --- MENTION LOGIC ---
    let mentionUsersData = [];

    async function toggleMentionSheet() {
        const sheet = document.getElementById('mentionSheet');
        const isHidden = sheet.classList.contains('hidden');
        
        // Hide other sheets
        const stickerSheet = document.getElementById('stickerSheet');
        const palette = document.getElementById('editorColorPalette');
        const textControls = document.getElementById('textControls');
        if (stickerSheet) stickerSheet.classList.add('hidden');
        if (palette) palette.classList.add('hidden');
        if (textControls) textControls.classList.add('hidden');
        
        if (isHidden) {
            sheet.classList.remove('hidden');
            loadMentionUsers();
        } else {
            sheet.classList.add('hidden');
        }
    }

    async function loadMentionUsers() {
        const loading = document.getElementById('mentionUsersLoading');
        const list = document.getElementById('mentionUsersList');
        
        // Hide loading, show search prompt
        if (loading) loading.classList.add('hidden');
        if (list) {
            list.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-primary-500/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-white text-base font-semibold mb-2">Search for users</p>
                    <p class="text-white/60 text-sm">Type a username to mention them</p>
                </div>
            `;
        }
    }

    function renderMentionUsers(users) {
        const list = document.getElementById('mentionUsersList');
        const loading = document.getElementById('mentionUsersLoading');
        if (loading) loading.classList.add('hidden');
        
        if (!users || users.length === 0) {
            list.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-white/30 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-white/60 text-sm">No users found</p>
                    <p class="text-white/40 text-xs mt-1">Try a different search</p>
                </div>
            `;
            return;
        }
        
        list.innerHTML = users.map(user => `
            <div onclick="addMention('${user.username}', ${user.id})" 
                 class="flex items-center space-x-2 p-2 rounded-lg cursor-pointer hover:bg-white/10 transition-colors"
                 data-username="${user.username.toLowerCase()}">
                <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name=' + user.username}" 
                     class="w-8 h-8 rounded-full border border-white/20" 
                     onerror="handleImageError(this)">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">${user.display_name || user.username}</p>
                    <p class="text-xs text-white/60 truncate">@${user.username}</p>
                </div>
            </div>
        `).join('');
    }

    let mentionSearchTimeout;
    async function filterMentionUsers(query) {
        clearTimeout(mentionSearchTimeout);
        
        const list = document.getElementById('mentionUsersList');
        const loading = document.getElementById('mentionUsersLoading');
        
        // If empty, show cached data
        if (!query.trim()) {
            renderMentionUsers(mentionUsersData);
            return;
        }
        
        // Show loading
        if (loading) loading.classList.remove('hidden');
        if (list) list.innerHTML = '';
        
        // Debounce search
        mentionSearchTimeout = setTimeout(async () => {
            try {
                // Strip @ from query if it exists
                const cleanQuery = query.startsWith('@') ? query.substring(1) : query;
                
                if (!cleanQuery.trim()) {
                    renderMentionUsers(mentionUsersData);
                    return;
                }

                // bridge.request already prepends /api/v1 - so we use /search/users
                const data = await window.bridge.request(`/search/users?q=${encodeURIComponent(cleanQuery)}&limit=20`);
                
                // Try all possible locations for the data
                let results = null;
                if (data?.data) {
                    results = Array.isArray(data.data) ? data.data : data.data.data || [];
                } else if (data?.users) {
                    results = data.users;
                } else if (data?.results) {
                    results = data.results;
                } else if (Array.isArray(data)) {
                    results = data;
                } else {
                    results = [];
                }
                
                if (loading) loading.classList.add('hidden');
                renderMentionUsers(results);
            } catch (err) {
                console.error('Search failed with error:', err);
                
                if (loading) loading.classList.add('hidden');
                if (list) {
                    list.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-400 text-sm mb-2">Search failed</p>
                            <p class="text-white/40 text-xs">Try again in a moment</p>
                        </div>
                    `;
                }
            }
        }, 300); // Wait 300ms after user stops typing
    }

    function addMention(username, userId) {
        const container = document.getElementById('editorCanvasContainer');
        const el = document.createElement('div');
        el.className = 'draggable-item mention-tag absolute px-4 py-2 rounded-lg bg-primary-500/80 backdrop-blur-sm text-white font-bold text-base cursor-move select-none transition-transform active:scale-110';
        el.innerText = '@' + username;
        el.dataset.userId = userId;
        el.dataset.username = username;
        el.dataset.mentionTag = 'true';
        el.style.left = '50%';
        el.style.top = '50%';
        el.style.transform = 'translate(-50%, -50%)';
        el.style.touchAction = 'none';
        
        // Add events
        el.onmousedown = dragStart;
        el.ontouchstart = dragStart;
        
        container.appendChild(el);
        toggleMentionSheet();
        
        window.toast(`Mentioned @${username}`, 'success');
    }

    async function publishStory() {
        if (!currentFile) return;
        
        const btn = document.querySelector('#storyEditor button[onclick="publishStory()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>';
        btn.disabled = true;

        try {
            // Process Image (Burn Filters & Text)
            const result = await processStoryImage();
            const processedBlob = result.blob;
            const mentions = result.mentions;

            const formData = new FormData();
            formData.append('type', 'image');
            formData.append('image', processedBlob, 'story.jpg');
            
            if (mentions && mentions.length > 0) {
                formData.append('mentions', JSON.stringify(mentions));
            }

            await window.bridge.request('/stories', {
                method: 'POST',
                body: formData
            });

            window.toast('Story added to your timeline', 'success');
            closeStoryEditor();
            setTimeout(() => loadStories(), 1000);
        } catch (err) {
            console.error(err);
            window.toast('Failed to upload story', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    function processStoryImage() {
        return new Promise((resolve, reject) => {
            const img = document.getElementById('editorPreview');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const container = document.getElementById('editorCanvasContainer');

            // Set dimensions match actual image natural size for quality
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;

            // 1. Draw Image with Filter
            const filter = filters[currentFilterIndex];
            if (filter) ctx.filter = filter;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            ctx.filter = 'none';

            // 2. Metrics for mapping
            const imgRatio = img.naturalWidth / img.naturalHeight;
            const contRect = container.getBoundingClientRect();
            const contRatio = contRect.width / contRect.height;
            let renderW, renderH, offsetX, offsetY;
            
            if (contRatio > imgRatio) { // Pillarbox
                renderH = contRect.height;
                renderW = renderH * imgRatio;
                offsetX = (contRect.width - renderW) / 2;
                offsetY = 0;
            } else { // Letterbox
                renderW = contRect.width;
                renderH = renderW / imgRatio;
                offsetX = 0;
                offsetY = (contRect.height - renderH) / 2;
            }
            const scale = img.naturalWidth / renderW;

            // 3. Burn Drawing Layer
            const drawLayer = document.getElementById('drawingLayer');
            if (drawLayer) {
                ctx.drawImage(drawLayer, offsetX, offsetY, renderW, renderH, 0, 0, canvas.width, canvas.height);
            }

            // 4. Collect Mentions & Burn Overlays
            const mentionsMetadata = [];
            const draggables = container.querySelectorAll('.draggable-item');
            
            draggables.forEach(el => {
                if (el.classList.contains('hidden')) return;
                
                // Get style pos
                let visualX, visualY;
                if (el.style.left.includes('%') || !el.style.left) {
                    visualX = contRect.width / 2; visualY = contRect.height / 2;
                } else {
                    visualX = parseFloat(el.style.left); visualY = parseFloat(el.style.top);
                }
                
                const relX = visualX - offsetX;
                const relY = visualY - offsetY;
                const canvasX = relX * scale;
                const canvasY = relY * scale;

                const text = el.innerText;

                // Capture Mention Metadata
                if (el.dataset.mentionTag === 'true') {
                    mentionsMetadata.push({
                        user_id: el.dataset.userId,
                        username: el.dataset.username,
                        x: (relX / renderW) * 100, // Percentage of image width
                        y: (relY / renderH) * 100  // Percentage of image height
                    });
                }
                
                // Burn Text to Canvas
                if (el.id === 'editorCaptionDisplay') {
                    const fontSize = Math.max(40, canvas.width / 15);
                    const chosenFont = fonts[currentFontIndex] || 'sans-serif';
                    ctx.font = `bold ${fontSize}px ${chosenFont}`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    const metrics = ctx.measureText(text);
                    const textH = fontSize;
                    const textW = metrics.width;
                    const pad = fontSize * 0.4;
                    
                    if (textBgMode !== 0) {
                        ctx.fillStyle = textBgMode === 1 ? 'rgba(0,0,0,0.5)' : (textColor === '#ffffff' ? '#ffffff' : textColor);
                        ctx.beginPath();
                        ctx.roundRect(canvasX - textW/2 - pad, canvasY - textH/2 - pad/2, textW + pad*2, textH + pad, 20);
                        ctx.fill();
                    }

                    if (textBgMode === 2) {
                         ctx.fillStyle = (textColor === '#ffffff' || textColor === '#ffff00' || textColor === '#00ffff' || textColor === '#orange') ? 'black' : 'white';
                    } else {
                        ctx.fillStyle = textColor;
                        ctx.strokeStyle = 'black';
                        ctx.lineWidth = fontSize / 15;
                        ctx.strokeText(text, canvasX, canvasY);
                    }
                    ctx.fillText(text, canvasX, canvasY);
                } else {
                    // Sticker or Mention (Visual only on image)
                    const fontSize = el.dataset.mentionTag === 'true' ? Math.max(30, canvas.width / 18) : Math.max(40, canvas.width / 10);
                    ctx.font = el.dataset.mentionTag === 'true' ? `bold ${fontSize}px sans-serif` : `${fontSize}px serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    if (el.dataset.mentionTag === 'true') {
                        // Background for mention tag on image
                        const metrics = ctx.measureText(text);
                        ctx.fillStyle = '#2D3FE6'; // Primary blue
                        ctx.beginPath();
                        ctx.roundRect(canvasX - metrics.width/2 - 15, canvasY - fontSize/2 - 5, metrics.width + 30, fontSize + 15, 12);
                        ctx.fill();
                        ctx.fillStyle = 'white';
                    } else {
                        ctx.fillStyle = 'white';
                    }
                    ctx.fillText(text, canvasX, canvasY);
                }
            });

            canvas.toBlob((blob) => {
                if (blob) resolve({ blob, mentions: mentionsMetadata });
                else reject(new Error('Canvas processing failed'));
            }, 'image/jpeg', 0.9);
        });
    }

    // Safe binding
    const addStoryBtn = document.getElementById('addStoryBtn');
    if (addStoryBtn) {
        addStoryBtn.onclick = () => document.getElementById('storyUpload').click();
    }
    

    // --- VISIBLE DEBUGGER ---
    // --- VISIBLE DEBUGGER ---
    function debugLog(msg, type = 'info') {
        let debugBox = document.getElementById('debug-console');
        if (!debugBox) {
            debugBox = document.createElement('div');
            debugBox.id = 'debug-console';
            debugBox.style.cssText = 'position:fixed;bottom:10px;right:10px;width:300px;max-height:200px;overflow-y:auto;background:rgba(0,0,0,0.8);color:#0f0;font-family:monospace;font-size:10px;padding:10px;z-index:9999;pointer-events:none;border-radius:8px;';
            document.body.appendChild(debugBox);
        }
        const line = document.createElement('div');
        line.style.color = type === 'error' ? '#ff5555' : '#00ff00';
        line.innerText = `[${new Date().toLocaleTimeString()}] ${msg}`;
        debugBox.prepend(line); // Newest on top
        console.log(`[DEBUG] ${msg}`);
    }

    let currentFeed = 'discovery';
    window.feedPosts = {}; // Global for actions

    function switchFeed(type) {
        if (currentFeed === type) return;
        currentFeed = type;
        
        // Update Buttons
        const btns = {
            discovery: document.getElementById('btn-discovery'),
            following: document.getElementById('btn-following')
        };
        
        Object.keys(btns).forEach(k => {
            if (k === type) {
                btns[k].classList.remove('text-text-tertiary', 'border-transparent');
                btns[k].classList.add('text-text-primary', 'border-primary-500');
            } else {
                btns[k].classList.remove('text-text-primary', 'border-primary-500');
                btns[k].classList.add('text-text-tertiary', 'border-transparent');
            }
        });
        
        loadFeed(true);
    }

    let feedSession = 0;

    async function loadFeed(reset = false) {
        if (isLoading) {
            debugLog('Load already in progress, skipping...');
            return;
        }
        
        isLoading = true;
        debugLog('Starting feed load...');
        
        if (reset) {
            feedSession++; 
            document.getElementById('feedContainer').innerHTML = '';
            document.getElementById('feedLoading')?.classList.remove('hidden');
            nextCursor = null;
        } else if (nextCursor) {
            // Show a small loader at the bottom for infinite scroll
            const bottomLoader = document.createElement('div');
            bottomLoader.id = 'bottom-loader';
            bottomLoader.className = 'py-8 flex justify-center';
            bottomLoader.innerHTML = '<div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>';
            document.getElementById('feedContainer').appendChild(bottomLoader);
        }

        const currentSession = feedSession;

        try {
            const endpoint = `/${currentFeed === 'discovery' ? 'posts/discovery' : 'posts/following'}${nextCursor ? '?cursor=' + nextCursor : ''}`;
            debugLog(`Requesting: ${endpoint}`);
            
            const response = await window.bridge.request(endpoint);
            if (currentSession !== feedSession) {
                debugLog('Request session expired, discarding results.');
                return;
            }
            debugLog(`Response received. Status: OK`);
            
            // Remove bottom loader if exists
            document.getElementById('bottom-loader')?.remove();
            
            const posts = response.data || [];
            debugLog(`Count: ${posts.length} posts`);
            
            renderPosts(posts);
            
            // Extract next cursor from response (Laravel CursorPaginator uses next_cursor or next_page_url with cursor param)
            nextCursor = response.next_cursor || null;
            if (!nextCursor && response.next_page_url) {
                const url = new URL(response.next_page_url, window.location.origin);
                nextCursor = url.searchParams.get('cursor');
            }
            
            const loader = document.getElementById('feedLoading');
            if(loader) loader.classList.add('hidden');

            if (posts.length === 0 && !nextCursor) {
                const container = document.getElementById('feedContainer');
                if (currentFeed === 'following') {
                    container.innerHTML += `
                        <div class="py-20 text-center space-y-4 anim-fade-in">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-bg-tertiary text-text-tertiary mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-text-primary">Quiet in the network...</h3>
                            <p class="text-sm text-text-tertiary max-w-xs mx-auto">You're not following anyone yet. Head over to <span class="text-primary-500 font-bold cursor-pointer" onclick="switchFeed('discovery')">For You</span> to discover content.</p>
                        </div>`;
                } else {
                    container.innerHTML += `<div class="p-8 text-center text-text-tertiary italic">End of the neural connection.</div>`;
                }
            }
        } catch (err) {
            debugLog(`Error: ${err.message || err}`, 'error');
            console.error(err);
            window.toast?.('Feed load failed', 'error');
        } finally {
            isLoading = false;
        }
    }


    function renderPosts(posts) {
        debugLog(`Rendering ${posts.length} posts...`);
        const container = document.getElementById('feedContainer');
        if (!container || !posts) {
            debugLog('Container missing or no posts', 'error');
            return;
        }

        try {
            posts.forEach(post => {
                window.feedPosts[post.id] = post; // Store for access
                
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = window.renderPostHtml(post, window.currentUser, 'feed');
                const newArticle = tempDiv.firstElementChild;
                container.appendChild(newArticle);

                // Prepare variables for Player Init
                let videoId = null;
                if (post.media && post.media.length > 0 && post.media[0].type === 'video') {
                     videoId = `video-${post.id}`;
                }

                // Initialize Player if Video is present
                if (videoId) {
                    setTimeout(() => {
                        const videoContainer = document.getElementById(videoId);
                        // Check if VideoPlayer exists
                        if (typeof VideoPlayer === 'undefined') {
                            debugLog('VideoPlayer class missing!', 'error');
                            return;
                        }

                        if (videoContainer && !feedPlayers[post.id]) {
                            try {
                                 const media = post.media[0];
                                 const sources = media.variants && Object.keys(media.variants).length > 0 
                                    ? { ...media.variants, 'original': media.url } 
                                    : media.url;

                                 feedPlayers[post.id] = new VideoPlayer(videoContainer, sources, {
                                    autoplay: false, 
                                    muted: true, 
                                    poster: media.thumbnail_url,
                                    postId: post.id,
                                    likesCount: post.likes_count,
                                    commentsCount: post.comments_count,
                                    isLiked: post.is_liked,
                                    caption: post.caption,
                                    user: post.user,
                                    fullPost: post,
                                    hideControls: true,
                                    hideSidebar: true,
                                    hideCaption: true
                                });
                                
                                // Attach Event Listeners
                                const v = feedPlayers[post.id].video;
                                if(v) {
                                    v.addEventListener('play', () => {
                                         if (viewTimers[post.id]) clearTimeout(viewTimers[post.id]);
                                         viewTimers[post.id] = setTimeout(() => {
                                             window.bridge.request(`/posts/${post.id}/view`, { method: 'POST' }).catch(() => {});
                                         }, 3000);
                                    });
                                    observeFeedVideo(feedPlayers[post.id], videoContainer);
                                }
                            } catch(e) {
                                debugLog(`Player Init Error: ${e.message}`, 'error');
                            }
                        }
                    }, 100);
                }
            });
            debugLog('Render complete.');
        } catch (e) {
            debugLog(`Render Crash: ${e.message}`, 'error');
            console.error(e);
        }
    }


    function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }

    // Video Click Handler - Routes to Reels or Modal
    function handleVideoClick(postId, isReel) {
        const post = window.feedPosts[postId];
        if (!post) return;
        
        // Prioritize explicit type over duration-based flag
        if (post.type === 'video' || post.type === 'reel' || isReel) {
            // Navigate to Immersive Player (Reels page)
            window.location.href = `/reels?start=${postId}`;
        } else {
            // Open comments sheet instead of modal
            if (window.openCommentsSheet) window.openCommentsSheet(postId, 'feed');
        }
    }

    // Pause all feed videos except one
    function pauseAllFeedVideos(exceptPostId = null) {
        Object.keys(feedPlayers).forEach(postId => {
            if (postId != exceptPostId && feedPlayers[postId]) {
                feedPlayers[postId].pause();
            }
        });
    }


    // Observe feed video for auto-play/pause
    function observeFeedVideo(player, container) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const postId = container.id.replace('video-', '');
                
                if (entry.isIntersecting) {
                    // Start loading source as soon as it nears the viewport
                    if (player && typeof player.prepareSource === 'function') {
                        player.prepareSource();
                    }

                    if (entry.intersectionRatio >= 0.2) {
                        // Video is 20%+ visible - auto play
                        pauseAllFeedVideos(postId); // Pause all others
                        setTimeout(() => {
                            if (player && player.video) {
                                player.play();
                            }
                        }, 100); 
                    } else {
                        // partially visible but not enough for play
                        if (player && player.video) {
                            player.pause();
                        }
                    }
                } else {
                    // Video NOT intersecting at all - Stop everything to save bandwidth
                    if (player && typeof player.stop === 'function') {
                        player.stop();
                    }
                }
            });
        }, {
            threshold: [0, 0.2], // 0 to detect completely leaving, 0.2 for play
            rootMargin: '100px'
        });

        observer.observe(container);
    }

    async function handleMediaInteraction(postId, event) {
        if (event.type === 'touchend') {
            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            
            const deltaX = Math.abs(touchEndX - touchStartX);
            const deltaY = Math.abs(touchEndY - touchStartY);

            // If user moved more than 10px, it's a scroll, ignore the tap
            if (deltaX > 10 || deltaY > 10) return;
            
            if (event.cancelable) event.preventDefault();
        }
        
        const curTime = new Date().getTime();
        const tapLen = curTime - lastFeedTap;
        const post = window.feedPosts[postId];
        const container = event.currentTarget;
        
        if (tapLen < 300 && tapLen > 0) {
            // Double Tap -> Like
            const article = document.getElementById(`post-${postId}`);
            const likeBtn = article.querySelector('.like-btn');
            
            if (!post.is_liked) {
                toggleLike(postId, likeBtn);
            } else {
                showFeedHeart(container);
            }
            lastFeedTap = 0;
            return;
        }
        
        lastFeedTap = curTime;
        
        // Single tap does nothing (as per user request)
    }

    function showFeedHeart(container) {
        const heart = container.querySelector('.quick-like-heart');
        if (!heart) return;
        heart.classList.remove('anim-heart');
        void heart.offsetWidth; // Force reflow
        heart.classList.add('anim-heart');
    }

    async function toggleLike(postId, btn) {
        const post = window.feedPosts[postId];
        if (!post || !btn) return;

        const icon = btn.querySelector('svg');
        const countEl = btn.querySelector('.likes-count') || (btn.nextElementSibling && btn.nextElementSibling.classList.contains('likes-count') ? btn.nextElementSibling : null);
        if (!countEl) return;
        
        // --- OPTIMISTIC UI START ---
        const wasLiked = post.is_liked;
        const initialCount = post.likes_count;
        
        // Toggle state locally
        post.is_liked = !wasLiked;
        post.likes_count = wasLiked ? initialCount - 1 : initialCount + 1;
        
        // Update UI immediately
        if (post.is_liked) {
            btn.classList.add('text-accent-500');
            btn.classList.remove('text-text-secondary');
            icon.classList.add('fill-accent-500');
            icon.setAttribute('fill', 'currentColor');
            
            // Show heart if it was triggered via double-tap (we'll see if we can detect or just show always if turning on)
            const article = document.getElementById(`post-${postId}`);
            const mediaContainer = article.querySelector('.group\\/media');
            if (mediaContainer) showFeedHeart(mediaContainer);
        } else {
            btn.classList.remove('text-accent-500');
            btn.classList.add('text-text-secondary');
            icon.classList.remove('fill-accent-500');
            icon.setAttribute('fill', 'none');
        }
        countEl.innerText = post.likes_count;
        // --- OPTIMISTIC UI END ---

        try {
            const data = await window.bridge.request(`/posts/${postId}/like`, { method: 'POST' });
            
            // Sync final state from server
            post.is_liked = data.data.liked;
            post.likes_count = data.data.likes_count;
            countEl.innerText = data.data.likes_count;
            
            // Update UI again if server disagreed (unlikely but safe)
            if (post.is_liked) {
                btn.classList.add('text-accent-500');
                icon.classList.add('fill-accent-500');
                icon.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('text-accent-500');
                icon.classList.remove('fill-accent-500');
                icon.setAttribute('fill', 'none');
            }
        } catch (err) {
            console.error('Like sync failed', err);
            // Revert to original state
            post.is_liked = wasLiked;
            post.likes_count = initialCount;
            countEl.innerText = initialCount;
            
            if (wasLiked) {
                btn.classList.add('text-accent-500');
                icon.classList.add('fill-accent-500');
                icon.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('text-accent-500');
                icon.classList.remove('fill-accent-500');
                icon.setAttribute('fill', 'none');
            }
            window.toast?.('Neural sync failed - reverted', 'error');
        }
    }

    // Submit Post


    // Search wiring
    const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && input.value.trim()) {
                window.location.href = `/explore?q=${encodeURIComponent(input.value)}`;
            }
        });
    });

    function openStoryComments() {
        if (!currentStories[storyIndex]) return;
        const storyId = currentStories[storyIndex].id;
        
        // Pause story progress
        isPaused = true;
        if (storyTimer) clearInterval(storyTimer);
        
        if (window.openCommentsSheet) {
            window.openCommentsSheet(storyId, 'stories');
            
            // Re-bind close event to resume story if needed
            const originalClose = window.closeCommentsSheet;
            window.closeCommentsSheet = function() {
                originalClose();
                isPaused = false;
                startStoryTimer(); // Resume progress
                window.closeCommentsSheet = originalClose; // Restore original
            };
        }
    }

    // --- SHARE STORY MODAL LOGIC ---
    let shareUsersData = [];
    let selectedShareUsers = new Set();
    let currentStoryToShare = null;

    async function openShareStoryModal() {
        if (!currentStories[storyIndex]) return;
        currentStoryToShare = currentStories[storyIndex];
        
        const modal = document.getElementById('shareStoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Pause story
        pauseStory();
        
        // Load followers/following
        await loadShareUsers();
    }

    function closeShareStoryModal() {
        const modal = document.getElementById('shareStoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset
        selectedShareUsers.clear();
        document.getElementById('shareSearchInput').value = '';
        document.getElementById('shareMessageInput').value = '';
        updateShareSelection();
        
        // Resume story
        resumeStory();
    }

    async function loadShareUsers() {
        const loading = document.getElementById('shareUsersLoading');
        const list = document.getElementById('shareUsersList');
        
        if (loading) loading.classList.remove('hidden');
        
        try {
            // Try multiple endpoints in order
            let data = null;
            
            // Try 1: Get following users
            try {
                data = await window.bridge.request('/users/following');
            } catch (e) {
                console.log('Following endpoint failed, trying followers...');
            }
            
            // Try 2: Get followers
            if (!data) {
                try {
                    data = await window.bridge.request('/users/followers');
                } catch (e) {
                    console.log('Followers endpoint failed, trying search...');
                }
            }
            
            // Try 3: Search all users (last resort)
            if (!data) {
                try {
                    data = await window.bridge.request('/search/users?limit=50');
                } catch (e) {
                    console.log('Search endpoint failed');
                }
            }
            
            shareUsersData = data?.data || data?.users || data?.results || [];
            
            if (loading) loading.classList.add('hidden');
            renderShareUsers(shareUsersData);
        } catch (err) {
            console.error('Failed to load users:', err);
            if (loading) loading.classList.add('hidden');
            if (list) {
                list.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-text-tertiary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-text-tertiary text-sm">No users found</p>
                        <p class="text-text-tertiary text-xs mt-2">Follow some users to share stories with them</p>
                    </div>
                `;
            }
        }
    }

    function renderShareUsers(users) {
        const list = document.getElementById('shareUsersList');
        const loading = document.getElementById('shareUsersLoading');
        loading.classList.add('hidden');
        
        if (!users || users.length === 0) {
            list.innerHTML = '<p class="text-center text-text-tertiary py-8">No users found</p>';
            return;
        }
        
        list.innerHTML = users.map(user => {
            const isSelected = selectedShareUsers.has(user.id);
            return `
                <div onclick="toggleShareUser(${user.id})" 
                     class="flex items-center space-x-3 p-3 rounded-xl cursor-pointer hover:bg-bg-tertiary transition-colors ${isSelected ? 'bg-bg-tertiary' : ''}" 
                     data-user-id="${user.id}" 
                     data-username="${user.username.toLowerCase()}">
                    <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name=' + user.username}" 
                         class="w-10 h-10 rounded-full border-2 ${isSelected ? 'border-primary-500' : 'border-border-light'}" 
                         onerror="handleImageError(this)">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-text-primary">${user.display_name || user.username}</p>
                        <p class="text-xs text-text-secondary">@${user.username}</p>
                    </div>
                    <div class="w-6 h-6 rounded-full border-2 ${isSelected ? 'border-primary-500 bg-primary-500' : 'border-border-light'} flex items-center justify-center transition-all">
                        ${isSelected ? '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function filterShareUsers(query) {
        if (!query.trim()) {
            renderShareUsers(shareUsersData);
            return;
        }
        
        const filtered = shareUsersData.filter(user => 
            user.username.toLowerCase().includes(query.toLowerCase()) ||
            (user.display_name && user.display_name.toLowerCase().includes(query.toLowerCase()))
        );
        renderShareUsers(filtered);
    }

    function toggleShareUser(userId) {
        if (selectedShareUsers.has(userId)) {
            selectedShareUsers.delete(userId);
        } else {
            selectedShareUsers.add(userId);
        }
        updateShareSelection();
    }

    function updateShareSelection() {
        const count = selectedShareUsers.size;
        document.getElementById('selectedUsersCount').innerText = `${count} selected`;
        
        const btn = document.getElementById('shareStoryBtn');
        if (count > 0) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
        
        // Update UI
        const items = document.querySelectorAll('[data-user-id]');
        items.forEach(item => {
            const userId = parseInt(item.dataset.userId);
            const isSelected = selectedShareUsers.has(userId);
            const checkbox = item.querySelector('div:last-child');
            const avatar = item.querySelector('img');
            
            if (isSelected) {
                item.classList.add('bg-bg-tertiary');
                avatar.classList.remove('border-border-light');
                avatar.classList.add('border-primary-500');
                checkbox.classList.remove('border-border-light');
                checkbox.classList.add('border-primary-500', 'bg-primary-500');
                checkbox.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
            } else {
                item.classList.remove('bg-bg-tertiary');
                avatar.classList.add('border-border-light');
                avatar.classList.remove('border-primary-500');
                checkbox.classList.add('border-border-light');
                checkbox.classList.remove('border-primary-500', 'bg-primary-500');
                checkbox.innerHTML = '';
            }
        });
    }

    async function sendSharedStory() {
        if (!currentStoryToShare || selectedShareUsers.size === 0) return;
        
        const btn = document.getElementById('shareStoryBtn');
        const originalText = btn.innerText;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mx-auto"></div>';
        btn.disabled = true;
        
        const message = document.getElementById('shareMessageInput').value.trim();
        
        try {
            const promises = Array.from(selectedShareUsers).map(userId => {
                return window.bridge.request('/messages/share-story', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: userId,
                        story_id: currentStoryToShare.id,
                        message: message || null
                    })
                });
            });
            
            await Promise.all(promises);
            
            window.toast(`Story shared with ${selectedShareUsers.size} ${selectedShareUsers.size === 1 ? 'person' : 'people'}!`, 'success');
            closeShareStoryModal();
        } catch (err) {
            console.error('Share failed:', err);
            window.toast('Failed to share story', 'error');
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }

    // Inits
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Home View Initializing...');
        loadStories();
        loadFeed();
        
        // Intersection Observer for Infinite Scroll
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && nextCursor && !isLoading) {
                loadFeed();
            }
        }, { threshold: 0.1 });
        const trigger = document.getElementById('loadMoreTrigger');
        if (trigger) observer.observe(trigger);
    });

    // --- POST ACTIONS (Edit, Delete, Hide, Report) ---
    // Moved to public/js/post-actions.js and layouts/app.blade.php
</script>
@include('partials.create-content-modal')
@endsection

