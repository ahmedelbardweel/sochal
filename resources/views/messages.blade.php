@extends('layouts.app')

@section('content')
<div class="flex flex-1 h-full bg-bg-primary overflow-hidden">
    @include('partials.sidebar')

    <!-- Conversations List -->
    <aside id="chatListPane" class="w-full md:w-80 lg:w-96 flex flex-col border-r border-border-light bg-bg-primary z-20">
        <div class="p-6 border-b border-border-light">
            <h2 class="text-2xl font-bold tracking-tight text-text-primary">Messages</h2>
            <div class="mt-4 relative">
                <input type="text" id="chatSearchInput" placeholder="Search" class="w-full h-10 bg-bg-secondary border border-border-light rounded-xl px-4 pl-10 text-xs text-text-primary focus:border-primary-500 transition-all">
                <svg class="absolute left-3 top-3 w-4 h-4 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div id="chatsContainer" class="flex-1 overflow-y-auto pb-safe">
            <!-- Loading -->
            <div id="chatsLoading" class="p-4 space-y-4">
                @for ($i = 1; $i <= 3; $i++)
                <div class="p-4 border-b border-border-light animate-pulse flex space-x-3">
                    <div class="w-12 h-12 bg-bg-tertiary rounded-2xl"></div>
                    <div class="flex-1 space-y-2"><div class="h-3 bg-bg-tertiary rounded w-1/2"></div><div class="h-2 bg-bg-tertiary rounded w-3/4"></div></div>
                </div>
                @endfor
            </div>
        </div>
    </aside>

    <!-- Chat Room -->
    <main id="chatRoomPane" class="hidden md:flex flex-1 bg-bg-secondary relative messenger-layout">
        <!-- Empty State -->
        <div id="noChatSelected" class="flex flex-1 flex-col items-center justify-center p-6 text-center">
            <div class="w-24 h-24 bg-bg-tertiary rounded-full flex items-center justify-center mb-4 text-4xl">💬</div>
            <h3 class="text-xl font-bold text-text-primary">Select a conversation</h3>
            <p class="text-sm text-text-secondary mt-2">Initialize your neural secure link to start chatting.</p>
        </div>

        <!-- Active Chat -->
        <div id="activeChat" class="hidden flex flex-col h-full w-full">
            <!-- Fixed Header -->
            <header class="messenger-fixed-top h-16 flex items-center justify-between px-6 bg-bg-primary/80 backdrop-blur-xl border-b border-border-light">
                <div class="flex items-center space-x-3">
                    <button class="md:hidden text-text-secondary mr-2" onclick="togglePane('list')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="relative">
                        <img id="activeAvatar" src="" class="w-10 h-10 rounded-xl object-cover bg-bg-tertiary shadow-sm">
                        <span id="activeOnlineStatus" class="hidden absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-bg-primary rounded-full"></span>
                    </div>
                    <div id="activeHeaderLabels" class="overflow-hidden">
                        <div class="flex items-center space-x-2">
                            <p id="activeName" class="text-sm font-bold text-text-primary truncate"></p>
                            <span id="neuralLinkBadge" class="text-[8px] px-1.5 py-0.5 rounded-full bg-bg-tertiary text-text-tertiary font-bold uppercase tracking-widest border border-border-light hidden">Neural Link</span>
                        </div>
                        <p class="text-[10px] text-green-500 font-bold uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                            Secure Connection
                        </p>
                    </div>
                </div>
            </header>

            <!-- Scrollable Message Area -->
            <div id="messagesContainer" class="messenger-content p-4 md:p-6 space-y-4 flex flex-col">
                <!-- Messages injected here -->
            </div>

            <!-- Fixed Footer / Input Area -->
            <footer class="messenger-fixed-bottom p-4 bg-bg-primary/80 backdrop-blur-xl border-t border-border-light">
                <!-- Typing Indicator (Now stays above input) -->
                <div id="typingIndicator" class="hidden absolute bottom-full left-4 mb-2 bg-bg-secondary/90 backdrop-blur-md px-4 py-2 rounded-2xl border border-border-light shadow-lg z-30 animate-slide-in">
                    <div class="flex items-center space-x-2">
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-primary-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                            <div class="w-1.5 h-1.5 bg-primary-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                            <div class="w-1.5 h-1.5 bg-primary-500 rounded-full animate-bounce"></div>
                        </div>
                        <span class="text-[10px] font-bold text-text-secondary uppercase tracking-wider tabular-nums" id="typingText"></span>
                    </div>
                </div>

                <form id="chatForm" class="flex items-center space-x-3 bg-bg-secondary rounded-2xl p-2 px-3 focus-within:bg-bg-primary border border-transparent focus-within:border-primary-500/30 transition-all shadow-sm">
                    <input type="text" id="msgInput" placeholder="Type a message..." class="flex-1 bg-transparent border-none focus:ring-0 focus:outline-none outline-none text-base md:text-sm text-text-primary h-10 px-1" autocomplete="off">
                    <button type="submit" class="p-2.5 bg-primary-500 text-white rounded-xl shadow-lg shadow-primary-500/20 hover:scale-105 active:scale-95 transition-all flex-none">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </form>
                <!-- Extra safe area padding for mobile when keyboard is hidden -->
                <div class="md:hidden pb-safe"></div>
            </footer>
        </div>
    </main>
</div>

<script>
    let activeChatId = @json($chatId);
    let selectedChatId = activeChatId;
    let isSearching = false;
    let lastTypingSent = 0;
    let currentOtherMember = null;

    // Search Logic
    const searchInput = document.getElementById('chatSearchInput');
    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);

        if (!query) {
            isSearching = false;
            loadChats();
            return;
        }

        searchTimeout = setTimeout(async () => {
            isSearching = true;
            try {
                const data = await window.bridge.request(`/search/users?q=${encodeURIComponent(query)}`);
                renderSearchResults(data.data || []);
            } catch (err) {
                console.error('Search error:', err);
            }
        }, 500);
    });

    function renderSearchResults(users) {
        const container = document.getElementById('chatsContainer');
        container.innerHTML = '<div class="p-4 text-[10px] font-black text-text-tertiary uppercase tracking-widest">Search Results</div>';

        if (users.length === 0) {
            container.innerHTML += '<div class="p-8 text-center text-xs text-text-tertiary italic">No users found</div>';
            return;
        }

        users.forEach(user => {
            const item = document.createElement('div');
            item.className = `flex items-center space-x-4 p-4 hover:bg-bg-secondary cursor-pointer border-b border-border-light transition-all`;
            item.innerHTML = `
                <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" class="w-12 h-12 rounded-2xl object-cover">
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold truncate text-text-primary">${user.display_name || user.username}</p>
                    <p class="text-[10px] text-text-tertiary">@${user.username}</p>
                </div>
            `;
            item.onclick = () => startChatWithUser(user.id);
            container.appendChild(item);
        });
    }

    async function startChatWithUser(userId) {
        try {
            window.toast('Establishing neural link...', 'info');
            const data = await window.bridge.request(`/chats/direct/${userId}`, { method: 'POST' });
            const chat = data.data;
            
            // Clear search and reload chats to show the new/existing one
            searchInput.value = '';
            isSearching = false;
            
            await loadChats();
            openChat(chat.id);
        } catch (err) {
            window.toast('Link failed', 'error');
        }
    }

    window.onlineUsers = window.onlineUsers || new Set();

    async function loadChats() {
        try {
            // Wait for currentUser if not ready
            if (!window.currentUser) {
                await initUser();
            }
            const data = await window.bridge.request('/chats');
            const chats = data.data || [];
            renderChats(chats);
            if (activeChatId) {
                const target = chats.find(c => String(c.id) === String(activeChatId));
                if (target) {
                    const other = target.users.find(u => String(u.id) !== String(window.currentUser?.id));
                    openChat(activeChatId, other);
                } else {
                    openChat(activeChatId);
                }
            }
            refreshActiveUserPresence();
        } catch (err) {
            console.error('System failed:', err);
            const errorMsg = typeof err === 'string' ? err : (err.message || 'Signal lost');
            window.toast(errorMsg, 'error');
        }
    }

    function renderChats(chats) {
        const container = document.getElementById('chatsContainer');
        container.innerHTML = '';

        chats.forEach(chat => {
            const lastMsg = chat.last_message || { content: 'No messages yet' };
            const otherMember = chat.users.find(m => String(m.id) !== String(window.currentUser?.id)) || { display_name: 'Unknown', username: 'unknown' };
            
            const item = document.createElement('div');
            const isActive = String(selectedChatId) === String(chat.id);
            item.className = `flex items-center space-x-4 p-4 hover:bg-bg-secondary/50 cursor-pointer border-b border-border-light transition-all active:scale-[0.98] ${isActive ? 'bg-primary-500/5 border-r-4 border-r-primary-500 shadow-inner' : ''}`;
            const isActuallyOnline = isUserOnline(otherMember.id, otherMember);
            item.innerHTML = `
                <div class="relative flex-shrink-0">
                    <img src="${otherMember.avatar_url || 'https://ui-avatars.com/api/?name='+otherMember.username}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-transparent group-hover:ring-primary-500/20 transition-all">
                    <span id="presence-indicator-${otherMember.id}" class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-bg-primary rounded-full presence-pulse ${isActuallyOnline ? '' : 'hidden'}"></span>
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex justify-between items-center mb-0.5">
                        <p class="text-sm font-bold truncate ${selectedChatId == chat.id ? 'text-primary-500' : 'text-text-primary'}">${otherMember.display_name || otherMember.username}</p>
                        <span class="text-[10px] text-text-tertiary font-medium">${chat.last_message_time || ''}</span>
                    </div>
                    <p class="text-xs text-text-secondary truncate leading-relaxed">${lastMsg.content || ''}</p>
                </div>
            `;
            item.onclick = () => openChat(chat.id, otherMember);
            container.appendChild(item);
        });
    }

    async function openChat(id, memberData = null) {
        // Leave previous channel if any (Ensure it's the instance, not the constructor function)
        if (selectedChatId && window.Echo && typeof window.Echo !== 'function') {
            window.Echo.leave(`chat.${selectedChatId}`);
        }

        selectedChatId = id;
        lastTypingSent = 0; // Reset typing debounce on new chat
        document.getElementById('noChatSelected').classList.add('hidden');
        document.getElementById('activeChat').classList.remove('hidden');
        if (window.innerWidth < 768) {
            togglePane('room');
        }

        if (memberData) {
            currentOtherMember = memberData;
            document.getElementById('activeName').innerText = memberData.display_name;
            document.getElementById('activeAvatar').src = memberData.avatar_url || `https://ui-avatars.com/api/?name=${memberData.username}`;
            document.getElementById('activeHeaderLabels').classList.remove('hidden');
            
            refreshActiveUserPresence();
        } else {
            currentOtherMember = null;
            document.getElementById('activeHeaderLabels').classList.add('hidden');
            document.getElementById('activeOnlineStatus').classList.add('hidden');
        }

        loadMessages(id);

        // --- SUB-CHANNEL: Neural Polling (Fallback for real-time) ---
        if (window.neuralPolling) clearInterval(window.neuralPolling);
        window.neuralPolling = setInterval(async () => {
            if (selectedChatId === id && document.visibilityState === 'visible') {
                try {
                    const data = await window.bridge.request(`/chats/${id}/messages`);
                    
                    // Hybrid Typing Check
                    if (data.typing) {
                        console.log(`[Neural Link] Typing detected: ${data.typing}`);
                        showTyping(data.typing);
                    } else {
                        hideTyping();
                    }

                    const messages = data.data || [];
                    // Only re-render if count changed or last message id changed
                    const currentCount = document.querySelectorAll('#messagesContainer .group').length;
                    if (messages.length > currentCount) {
                        console.log('[Neural Link] New signals detected via polling.');
                        renderMessages(messages);
                    }
                } catch (e) {
                    console.warn('[Neural Link] Polling pulse failed:', e);
                }
            }
        }, 2000); // Accelerated 2s pulse

        // --- MAIN-CHANNEL: Real-time Echo ---
        if (window.Echo && typeof window.Echo !== 'function') {
            console.log(`[Neural Link] Subscribing to chat.${id}`);
            window.Echo.private(`chat.${id}`)
                .subscribed(() => {
                    console.log(`[Neural Link] Successfully joined chat.${id}`);
                    if (window.neuralPolling) clearInterval(window.neuralPolling); // Stop polling if Echo works
                })
                .listen('.message.sent', (e) => {
                    console.log(`[Neural Link] Signal received:`, e);
                    if (e.sender_id !== window.currentUser.id) {
                        appendMessage(e);
                        hideTyping();
                    }
                })
                .listenForWhisper('typing', (e) => {
                    showTyping(e.name);
                })
                .error((err) => {
                    console.error(`[Neural Link] Connection failed for chat.${id}:`, err);
                });
        }
    }

    function refreshActiveUserPresence() {
        if (!currentOtherMember) return;
        
        const labels = document.getElementById('activeHeaderLabels');
        const statusIcon = document.getElementById('activeOnlineStatus');
        if (!labels || !statusIcon) return;
        
        const statusText = labels.querySelector('p:last-child');
        if (!statusText) return;

        const isActuallyOnline = isUserOnline(currentOtherMember.id, currentOtherMember);
        
        if (isActuallyOnline) {
            statusIcon.classList.remove('hidden');
            statusIcon.classList.add('presence-pulse');
            statusText.innerHTML = `
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                Active Now
            `;
            statusText.className = 'text-[10px] text-green-500 font-bold uppercase tracking-widest flex items-center anim-slide-in';
        } else {
            statusIcon.classList.add('hidden');
            statusIcon.classList.remove('presence-pulse');
            statusText.innerHTML = `
                Last seen ${currentOtherMember.last_active_human || 'Recently'}
            `;
            statusText.className = 'text-[10px] text-text-tertiary font-bold uppercase tracking-wider flex items-center anim-slide-in';
        }
    }

    let currentConnectionState = window.NeuralPresence ? window.NeuralPresence.state : 'connecting';

    function isUserOnline(userId, userObject = null) {
        // Source of Truth logic for "Instant Reactivity"
        const inSocket = window.NeuralPresence && window.NeuralPresence.isOnline(userId);
        
        // If we are actively linked via Neural Link, the socket is the 100% source of truth
        if (currentConnectionState === 'connected') {
            return inSocket;
        }
        
        // Fallback: If socket is lost/syncing, use both
        if (inSocket) return true;
        if (userObject && userObject.is_online) return true;
        
        return false;
    }

    let typingTimeout;
    function showTyping(name) {
        if (!name) return;
        const indicator = document.getElementById('typingIndicator');
        const text = document.getElementById('typingText');
        text.innerText = `${name} جاري الكتابة...`;
        indicator.classList.remove('hidden');
        
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(hideTyping, 4000); // 4s timeout for better visibility
    }

    function hideTyping() {
        document.getElementById('typingIndicator').classList.add('hidden');
    }

    // Input Typing Sync
    document.getElementById('msgInput').addEventListener('input', () => {
        if (selectedChatId && window.Echo && typeof window.Echo !== 'function') {
            window.Echo.private(`chat.${selectedChatId}`)
                .whisper('typing', {
                    name: window.currentUser.display_name
                });
        }

        // Hybrid Fallback: Send API pulse every 2s while active
        const now = Date.now();
        if (selectedChatId && (now - lastTypingSent > 2000)) {
            lastTypingSent = now;
            console.log(`[Neural Link] Sending typing pulse for chat ${selectedChatId}`);
            window.bridge.request(`/chats/${selectedChatId}/typing`, { method: 'POST' }).catch(() => {});
        }
    });

    function appendMessage(msg, shouldScroll = true) {
        hideTyping(); // Clear indicator when new message arrives
        const container = document.getElementById('messagesContainer');
        const isMe = (msg.sender_id || msg.user_id) === window.currentUser?.id;
        const bubble = document.createElement('div');
        bubble.className = `max-w-[80%] ${isMe ? 'self-end' : 'self-start'} group mb-2 anim-slide-in`;
        
        bubble.innerHTML = `
            <div class="flex items-end space-x-2 ${isMe ? 'flex-row-reverse space-x-reverse' : ''}">
                <div class="px-4 py-3 ${isMe ? 'bg-primary-500 text-white rounded-br-none' : 'bg-bg-primary text-text-primary rounded-bl-none border border-border-light'} rounded-2xl shadow-sm text-sm">
                    ${msg.content}
                </div>
            </div>
            <span class="text-[10px] text-text-tertiary mt-1 block ${isMe ? 'text-right' : ''}">${msg.created_at_human || 'Just now'}</span>
        `;
        container.appendChild(bubble);
        if (shouldScroll) scrollToBottom();
    }

    window.scrollToBottom = function() {
        const container = document.getElementById('messagesContainer');
        if (!container) return;
        setTimeout(() => {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        }, 50);
    }

    async function loadMessages(id) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = '<div class="flex-1 flex items-center justify-center p-20 animate-pulse text-text-tertiary">Connecting...</div>';
        
        try {
            const data = await window.bridge.request(`/chats/${id}/messages`);
            renderMessages(data.data || []); // Laravel default pagination is .data
        } catch (err) {
            console.error('Message load error:', err);
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = '';
        
        // Use slice().reverse() if data is descending
        messages.slice().reverse().forEach(msg => appendMessage(msg, false));
        scrollToBottom();
    }

    function togglePane(view) {
        if (view === 'list') {
            document.getElementById('chatListPane').classList.remove('hidden');
            document.getElementById('chatRoomPane').classList.add('hidden');
            selectedChatId = null;
        } else {
            document.getElementById('chatListPane').classList.add('hidden');
            document.getElementById('chatRoomPane').classList.remove('hidden');
        }
    }

    document.getElementById('chatForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('msgInput');
        const content = input.value.trim();
        if (!content || !selectedChatId) return;

        input.value = '';
        
        // Optimistic update
        appendMessage({
            sender_id: window.currentUser.id,
            content: content,
            created_at_human: 'Just now'
        });

        try {
            await window.bridge.request(`/chats/${selectedChatId}/messages`, {
                method: 'POST',
                body: JSON.stringify({ content })
            });
            // No need to reload, real-time will handle if needed or append handles locally
        } catch (err) {
            console.error('Message failed:', err);
            const errorMsg = err.message || (err.error) || (typeof err === 'string' ? err : 'Signal lost');
            window.toast(errorMsg, 'error');
        }
    });

    // Safety interval to ensure presence is refreshed even if events are missed
    setInterval(() => {
        refreshActiveUserPresence();
    }, 10000);

    // Hybrid Presence Sync: Refresh UI every 30 seconds to update DB fallback state
    setInterval(() => {
        if (!isSearching) {
            loadChats();
        }
    }, 30000);

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('is-messenger');
        
        // Initial Connection State UI sync
        const badge = document.getElementById('neuralLinkBadge');
        if (badge && window.NeuralPresence) {
            badge.classList.remove('hidden');
            updateNeuralBadge(window.NeuralPresence.state);
        }
        
        loadChats();
    });

    function updateNeuralBadge(state) {
        const badge = document.getElementById('neuralLinkBadge');
        if (!badge) return;
        
        badge.classList.remove('hidden');
        if (state === 'connected') {
            badge.innerText = 'Neural Link: Linked';
            badge.className = 'text-[8px] px-1.5 py-0.5 rounded-full bg-green-500/10 text-green-500 font-bold uppercase tracking-widest border border-green-500/20';
        } else if (state === 'connecting') {
            badge.innerText = 'Neural Link: Syncing...';
            badge.className = 'text-[8px] px-1.5 py-0.5 rounded-full bg-yellow-500/10 text-yellow-500 font-bold uppercase tracking-widest border border-yellow-500/20';
        } else {
            badge.innerText = 'Neural Link: Lost';
            badge.className = 'text-[8px] px-1.5 py-0.5 rounded-full bg-red-500/10 text-red-500 font-bold uppercase tracking-widest border border-red-500/20';
        }
    }

    // Listen for real-time presence changes via professional NeuralPresence system
    if (window.NeuralPresence) {
        window.NeuralPresence.statusChange((data) => {
            // Surgical UI updates for "Instant" feel
            if (data.user) {
                const userId = String(data.user.id);
                const indicator = document.getElementById(`presence-indicator-${userId}`);
                
                if (indicator) {
                    if (data.type === 'joining' || data.type === 'here') {
                        indicator.classList.remove('hidden');
                    } else if (data.type === 'leaving') {
                        indicator.classList.add('hidden');
                    }
                }

                // Force local state override to bypass DB fallback lag
                if (currentOtherMember && userId === String(currentOtherMember.id)) {
                    if (data.type === 'leaving') {
                        currentOtherMember.is_online = false;
                        currentOtherMember.last_active_human = 'Just now';
                    } else if (data.type === 'joining') {
                        currentOtherMember.is_online = true;
                    }
                    refreshActiveUserPresence();
                }
            }

            if (data.type === 'reset' || data.type === 'here') {
                refreshActiveUserPresence();
            }
        });

        // Connection State Handling
        window.NeuralPresence.events.on('connection-state', (state) => {
            currentConnectionState = state;
            updateNeuralBadge(state);
        });
    }
</script>
@endsection
