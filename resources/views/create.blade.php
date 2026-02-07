@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary text-text-primary font-sans selection:bg-primary-500/30 overflow-x-hidden transition-colors duration-300">
    
    @include('partials.sidebar')

    <!-- Creative Hub -->
    <div class="flex-1 relative flex flex-col min-h-screen">
        
        <!-- Atmospheric Background -->
        <div class="fixed inset-0 pointer-events-none z-0">
            <div class="absolute -top-[10%] -left-[5%] w-[50%] h-[50%] bg-primary-600/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[0%] right-[0%] w-[40%] h-[40%] bg-accent-600/5 rounded-full blur-[100px]"></div>
        </div>

        <!-- Mobile Header -->
        <header class="sticky top-0 z-50 lg:hidden flex items-center justify-between px-4 h-16 bg-black/60 backdrop-blur-xl border-b border-white/5">
            <button onclick="window.history.back()" class="p-2 -ml-2 text-white/60 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h1 class="text-sm font-black tracking-widest uppercase italic">Create</h1>
            <div class="w-10"></div> <!-- Spacer -->
        </header>

        <div class="flex-1 flex flex-col lg:flex-row relative z-10 w-full max-w-7xl mx-auto lg:p-8 pb-24">
        
        <!-- Left Column: Media Workspace -->
        <div class="flex-1 flex flex-col p-4 lg:p-0">
            <div id="mediaZone" 
                 class="relative aspect-[4/5] lg:aspect-auto lg:flex-1 rounded-[2.5rem] bg-white/[0.02] border border-white/10 overflow-hidden group cursor-pointer flex flex-col items-center justify-center transition-all duration-700 hover:border-primary-500/30 shadow-2xl"
                 onclick="document.getElementById('mediaInput').click()">
                
                <!-- Inner Depth -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40 pointer-events-none"></div>

                <!-- Empty State -->
                <div id="mediaPlaceholder" class="flex flex-col items-center text-center px-6 z-20">
                    <div class="w-20 h-20 rounded-3xl bg-white/[0.03] border border-white/10 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-xl">
                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h2 class="text-xl font-black tracking-tight mb-2">ADD CONTENT</h2>
                    <p class="text-xs text-white/20 font-medium uppercase tracking-widest max-w-[200px]">Touch to select an image or video for your story</p>
                </div>

                <!-- Preview Layers -->
                <img id="previewImg" class="absolute inset-0 w-full h-full object-cover hidden z-10 animate-fade-in">
                <video id="previewVid" class="absolute inset-0 w-full h-full object-cover hidden z-10 animate-fade-in" loop muted playsinline></video>
                
                <!-- Overlay Shadow when preview active -->
                <div id="previewOverlay" class="absolute inset-0 bg-black/20 hidden z-15 pointer-events-none"></div>

                <!-- Floating Clear Button -->
                <button type="button" id="clearBtn" onclick="clearMedia(event)" class="absolute top-6 right-6 z-30 w-12 h-12 rounded-2xl bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white/40 hover:text-white hover:bg-accent-600/80 transition-all opacity-0 scale-90 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto shadow-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <input type="file" id="mediaInput" class="hidden" accept="image/*,video/*" onchange="handleFileSelect(this)">
            </div>

            <!-- Asset Metadata (Desktop) -->
            <div class="hidden lg:flex mt-6 px-10 items-center justify-between text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">
                <div class="flex gap-12">
                    <div class="flex flex-col gap-1">
                        <span class="text-white/10">Format</span>
                        <span class="text-white/40" id="fmt-info">---</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-white/10">Resolution</span>
                        <span class="text-white/40" id="res-info">---</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-primary-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                    <span>System Online</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Console -->
        <div class="lg:w-[400px] flex flex-col p-4 lg:p-0 lg:ml-8 gap-6">
            
            <!-- Type Switcher -->
            <div class="flex p-1.5 bg-white/[0.03] rounded-[1.5rem] border border-white/5 shadow-inner">
                <button onclick="setContentType('post')" id="type-post" class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 bg-white text-black shadow-xl">Post</button>
                <button onclick="setContentType('reel')" id="type-reel" class="flex-1 py-3 rounded-2xl text-[10px] font-bold text-white/30 hover:text-white uppercase tracking-widest transition-all duration-500">Reel</button>
                <button onclick="setContentType('video')" id="type-video" class="flex-1 py-3 rounded-2xl text-[10px] font-bold text-white/30 hover:text-white uppercase tracking-widest transition-all duration-500">Video</button>
            </div>

            <!-- Composition Box -->
            <div class="bg-white/[0.03] rounded-[2.5rem] border border-white/5 p-8 flex flex-col gap-8 shadow-2xl relative overflow-hidden">
                
                <!-- Content Area -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Description</label>
                        <span class="text-[10px] font-black text-white/10 uppercase tracking-widest" id="charCount">0/2200</span>
                    </div>
                    <div class="relative">
                        <textarea id="caption" 
                                  rows="6" 
                                  class="w-full bg-white/[0.02] border border-white/5 rounded-[1.5rem] px-6 py-6 text-white placeholder:text-white/20 focus:bg-white/[0.04] focus:border-white/10 transition-all outline-none resize-none text-[13px] leading-relaxed scrollbar-hide" 
                                  placeholder="Write something worth sharing..." 
                                  oninput="handleCaptionInput(this)"></textarea>
                        
                        <!-- Mentions Dropdown -->
                        <div id="mentionsDropdown" class="absolute left-0 right-0 bottom-full mb-3 bg-[#0a0a0a]/95 backdrop-blur-3xl rounded-[1.5rem] border border-white/10 shadow-2xl hidden max-h-56 overflow-y-auto z-50 p-2 scrollbar-hide animate-slide-up">
                            <div id="mentionsList" class="space-y-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Rich Actions -->
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" class="flex items-center gap-4 p-5 bg-white/[0.02] border border-white/5 rounded-3xl hover:bg-white/[0.05] hover:border-white/10 transition-all group/opt">
                        <div class="w-10 h-10 rounded-2xl bg-primary-500/10 flex items-center justify-center text-primary-500 group-hover/opt:scale-110 transition-transform">
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] font-black text-white/20 uppercase tracking-widest">Geo</p>
                            <p class="text-[11px] font-black text-white/60">Location</p>
                        </div>
                    </button>
                    <button type="button" class="flex items-center gap-4 p-5 bg-white/[0.02] border border-white/5 rounded-3xl hover:bg-white/[0.05] hover:border-white/10 transition-all group/opt">
                        <div class="w-10 h-10 rounded-2xl bg-accent-500/10 flex items-center justify-center text-accent-500 group-hover/opt:scale-110 transition-transform">
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] font-black text-white/20 uppercase tracking-widest">Scope</p>
                            <p class="text-[11px] font-black text-white/60">Public</p>
                        </div>
                    </button>
                </div>

                <!-- Video Thumbnail Section -->
                <div id="videoOptions" class="hidden animate-fade-in space-y-4 pt-2">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em] ml-2">Video Cover</label>
                    <div class="flex gap-5 items-center p-6 bg-white/[0.02] border border-white/5 rounded-[1.5rem] relative overflow-hidden group/cover cursor-pointer" onclick="document.getElementById('thumbnailInput').click()">
                        <div class="relative w-24 h-24 rounded-2xl bg-white/[0.05] border border-white/10 overflow-hidden flex-shrink-0">
                            <img id="thumbnailPreview" class="w-full h-full object-cover hidden transition-transform duration-1000 group-hover/cover:scale-110">
                            <div id="thumbnailPlaceholder" class="absolute inset-0 flex items-center justify-center opacity-30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-[12px] font-black text-white tracking-widest uppercase mb-1">Thumbnail</p>
                            <p class="text-[10px] text-white/20 font-bold uppercase tracking-wider leading-relaxed">Select a frame that defines your clip</p>
                        </div>
                        <input type="file" id="thumbnailInput" class="hidden" accept="image/*" onchange="handleThumbnailSelect(this)">
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="mt-4">
                    <form id="creationForm">
                        <input type="hidden" id="contentType" value="post">
                        <input type="hidden" id="privacyType" value="public">
                        
                        <button type="submit" id="submitBtn" class="w-full py-6 bg-white text-black rounded-[1.5rem] font-black text-[11px] tracking-[0.2em] uppercase transition-all duration-500 hover:scale-[1.02] active:scale-[0.98] shadow-[0_20px_40px_rgba(255,255,255,0.1)] relative overflow-hidden group/submit">
                            <span id="btnLabel" class="relative z-10">Share Post</span>
                            
                            <div id="loader" class="hidden absolute inset-0 bg-primary-500 flex items-center justify-center z-20">
                                <div class="flex items-center gap-4">
                                    <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    <span id="progressPercent" class="text-[13px] font-black tracking-tighter">0%</span>
                                    <button type="button" onclick="cancelUpload()" class="text-[9px] font-black uppercase tracking-widest text-white/60 hover:text-white transition-colors">Abort</button>
                                </div>
                            </div>

                            <div id="progressBar" class="absolute left-0 bottom-0 h-1 bg-primary-600/50 transition-all duration-300 pointer-events-none" style="width: 0%"></div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Note (Desktop) -->
            <p class="hidden lg:block text-center text-[9px] font-black text-white/10 uppercase tracking-[0.3em] px-10 leading-relaxed">
                By publishing, you agree to our creative guidelines and synchronization protocols.
            </p>
        </div>
    </div>
</div>

<script>
    let selectedFile = null;
    let selectedThumbnail = null;
    let videoDuration = 0;
    let currentUploadXhr = null;

    function setContentType(type) {
        document.getElementById('contentType').value = type;
        document.getElementById('btnLabel').innerText = `Share ${type}`;
        
        ['post', 'reel', 'video'].forEach(t => {
            const btn = document.getElementById(`type-${t}`);
            if (t === type) {
                btn.className = "flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 bg-white text-black shadow-xl";
            } else {
                btn.className = "flex-1 py-3 rounded-2xl text-[10px] font-bold text-white/30 hover:text-white uppercase tracking-widest transition-all duration-500";
            }
        });

        const accept = type === 'post' ? 'image/*,video/*' : 'video/*';
        document.getElementById('mediaInput').accept = accept;
        
        if (selectedFile && type !== 'post' && selectedFile.type.startsWith('image/')) {
            clearMedia();
        }
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            selectedFile = input.files[0];
            const reader = new FileReader();

            document.getElementById('mediaPlaceholder').classList.add('hidden');
            document.getElementById('previewImg').classList.add('hidden');
            document.getElementById('previewVid').classList.add('hidden');
            document.getElementById('videoOptions').classList.add('hidden');
            document.getElementById('clearBtn').classList.remove('opacity-0', 'scale-90', 'pointer-events-none');
            
            document.getElementById('fmt-info').innerText = selectedFile.type.split('/')[1].toUpperCase();

            if (selectedFile.type.startsWith('image/')) {
                reader.onload = (e) => {
                    const img = document.getElementById('previewImg');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    img.onload = () => {
                        document.getElementById('res-info').innerText = `${img.naturalWidth} x ${img.naturalHeight}`;
                    };
                };
                reader.readAsDataURL(selectedFile);
            } else if (selectedFile.type.startsWith('video/')) {
                const vid = document.getElementById('previewVid');
                const url = URL.createObjectURL(selectedFile);
                vid.src = url;
                vid.classList.remove('hidden');
                document.getElementById('videoOptions').classList.remove('hidden');
                vid.onloadedmetadata = () => {
                    videoDuration = vid.duration;
                    document.getElementById('res-info').innerText = `${vid.videoWidth}x${vid.videoHeight} / ${Math.round(vid.duration)}s`;
                };
            }
        }
    }

    function handleThumbnailSelect(input) {
        if (input.files && input.files[0]) {
            selectedThumbnail = input.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById('thumbnailPreview');
                img.src = e.target.result;
                img.classList.remove('hidden');
                document.getElementById('thumbnailPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(selectedThumbnail);
        }
    }

    function clearMedia(e) {
        if(e) e.stopPropagation();
        selectedFile = null;
        selectedThumbnail = null;
        document.getElementById('mediaInput').value = '';
        document.getElementById('thumbnailInput').value = '';
        document.getElementById('mediaPlaceholder').classList.remove('hidden');
        document.getElementById('previewImg').classList.add('hidden');
        document.getElementById('previewVid').classList.add('hidden');
        document.getElementById('videoOptions').classList.add('hidden');
        document.getElementById('thumbnailPreview').classList.add('hidden');
        document.getElementById('thumbnailPlaceholder').classList.remove('hidden');
        document.getElementById('clearBtn').classList.add('opacity-0', 'scale-90', 'pointer-events-none');
        document.getElementById('fmt-info').innerText = '---';
        document.getElementById('res-info').innerText = '---';
    }

    function handleCaptionInput(el) {
        document.getElementById('charCount').innerText = el.value.length + '/2200';
        const cursorPosition = el.selectionStart;
        const textBeforeCursor = el.value.substring(0, cursorPosition);
        const words = textBeforeCursor.split(/\s/);
        const lastWord = words[words.length - 1];

        if (lastWord.startsWith('@') && lastWord.length > 1) {
            searchMentions(lastWord.substring(1));
        } else {
            document.getElementById('mentionsDropdown').classList.add('hidden');
        }
    }

    let mentionTimeout = null;
    async function searchMentions(query) {
        clearTimeout(mentionTimeout);
        mentionTimeout = setTimeout(async () => {
            try {
                const data = await window.bridge.request(`/search/users?q=${encodeURIComponent(query)}&limit=5`);
                const users = data?.data || data?.users || (Array.isArray(data) ? data : []);
                renderMentionsList(users);
            } catch (err) { console.error(err); }
        }, 300);
    }

    function renderMentionsList(users) {
        const dropdown = document.getElementById('mentionsDropdown');
        const list = document.getElementById('mentionsList');
        if (!users || users.length === 0) { dropdown.classList.add('hidden'); return; }
        dropdown.classList.remove('hidden');
        list.innerHTML = users.map(user => `
            <div onclick="insertMention('${user.username}')" class="flex items-center gap-4 p-4 hover:bg-white/[0.05] rounded-[1.5rem] cursor-pointer transition-all border border-transparent hover:border-white/10 group/mention">
                <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name=' + user.username}" class="w-10 h-10 rounded-xl object-cover">
                <div>
                    <p class="text-[12px] font-black text-white uppercase tracking-tight">${user.display_name || user.username}</p>
                    <p class="text-[8px] text-white/20 font-black tracking-widest uppercase">@${user.username}</p>
                </div>
            </div>
        `).join('');
    }

    function insertMention(username) {
        const el = document.getElementById('caption');
        const cursorPosition = el.selectionStart;
        const textBeforeCursor = el.value.substring(0, cursorPosition);
        const textAfterCursor = el.value.substring(cursorPosition);
        const words = textBeforeCursor.split(/\s/);
        words[words.length - 1] = '@' + username + ' ';
        el.value = words.join(' ') + textAfterCursor;
        document.getElementById('mentionsDropdown').classList.add('hidden');
        el.focus();
    }

    function cancelUpload() {
        if (currentUploadXhr) {
            currentUploadXhr.abort();
            currentUploadXhr = null;
            document.getElementById('loader').classList.add('hidden');
            document.getElementById('submitBtn').disabled = false;
            window.toast?.('UPLOAD ABORTED', 'info');
        }
    }

    document.getElementById('creationForm').onsubmit = async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const loader = document.getElementById('loader');
        const progress = document.getElementById('progressBar');
        const progressPercent = document.getElementById('progressPercent');

        if (!selectedFile && !document.getElementById('caption').value) {
            window.toast?.('Payload empty', 'warning');
            return;
        }

        try {
            btn.disabled = true;
            loader.classList.remove('hidden');
            
            const formData = new FormData();
            formData.append('type', document.getElementById('contentType').value);
            formData.append('caption', document.getElementById('caption').value);
            formData.append('privacy', document.getElementById('privacyType').value);
            if (selectedFile) formData.append('media[]', selectedFile);
            if (selectedThumbnail) formData.append('thumbnail', selectedThumbnail);
            if (videoDuration > 0) formData.append('media_duration[]', Math.round(videoDuration));

            const xhr = new XMLHttpRequest();
            currentUploadXhr = xhr;
            xhr.open('POST', '/api/v1/posts', true);
            
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
            
            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    progress.style.width = percent + '%';
                    progressPercent.innerText = percent + '%';
                }
            };

            await new Promise((resolve, reject) => {
                xhr.onload = () => (xhr.status >= 200 && xhr.status < 300) ? resolve() : reject(new Error('Transmission Interrupted'));
                xhr.onerror = () => reject(new Error('Network Failed'));
                xhr.onabort = () => reject(new Error('Aborted'));
                xhr.send(formData);
            });

            window.toast?.('Published successfully!', 'success');
            setTimeout(() => window.location.href = '/home', 1000);

        } catch (err) {
            if (err.message !== 'Aborted') {
                btn.disabled = false;
                loader.classList.add('hidden');
                window.toast?.(err.message, 'error');
            }
        } finally {
            currentUploadXhr = null;
        }
    };
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: scale(1.02); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-up { animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
