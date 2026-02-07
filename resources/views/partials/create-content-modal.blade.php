<div id="createContentModal" class="fixed inset-0 z-[150] bg-black/60 hidden backdrop-blur-sm anim-fade-in flex items-end sm:items-center justify-center pointer-events-auto" onclick="closeCreateContentModal()">
    <div class="w-full sm:w-[400px] bg-[#1a1a1a] rounded-t-2xl sm:rounded-2xl border-t sm:border border-white/10 overflow-hidden transform transition-all translate-y-full anim-slide-up" onclick="event.stopPropagation()">
        <!-- Handle for mobile -->
        <div class="w-full h-1 mt-2 flex justify-center sm:hidden">
            <div class="w-12 h-1 bg-white/20 rounded-full"></div>
        </div>

        <div class="p-6 space-y-4">
            <h3 class="text-white text-lg font-bold text-center mb-6">Create Content</h3>
            
            <button onclick="document.getElementById('storyUpload').click(); closeCreateContentModal()" class="w-full flex items-center space-x-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 active:scale-98 transition-all group">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-white font-bold">Add to Story</p>
                    <p class="text-white/50 text-xs">Share a photo or video</p>
                </div>
            </button>

            <button onclick="startLive(); closeCreateContentModal()" class="w-full flex items-center space-x-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 active:scale-98 transition-all group">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-red-500 to-pink-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-white font-bold">Live</p>
                    <p class="text-white/50 text-xs">Start a live broadcast</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
    function openCreateContentModal() {
        const modal = document.getElementById('createContentModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.firstElementChild.classList.remove('translate-y-full');
        }, 10);
    }

    function closeCreateContentModal() {
        const modal = document.getElementById('createContentModal');
        modal.firstElementChild.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
