@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary">
    @include('partials.sidebar')

    <main class="flex-1 max-w-2xl mx-auto px-4 py-6 md:py-8">
        <!-- Tabs Header -->
        <div class="flex items-center space-x-8 mb-6 px-2 border-b border-border-light sticky top-0 bg-bg-primary/95 backdrop-blur-md z-30 pt-4">
            <button id="tabNotifications" onclick="switchTab('notifications')" 
                class="pb-3 text-sm md:text-base font-bold transition-all relative">
                Notifications
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500 rounded-full transform scale-x-0 transition-transform duration-300 origin-left" id="indicatorNotifications"></div>
            </button>
            <button id="tabRequests" onclick="switchTab('requests')" 
                class="pb-3 text-sm md:text-base font-bold transition-all relative text-text-tertiary">
                Follow Requests
                <span id="requestsBadge" class="hidden ml-2 bg-primary-500 text-white text-[10px] px-1.5 py-0.5 rounded-full"></span>
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500 rounded-full transform scale-x-0 transition-transform duration-300 origin-left" id="indicatorRequests"></div>
            </button>
        </div>

        <!-- Notifications View -->
        <div id="viewNotifications" class="section-view transition-opacity duration-300">
            <div class="flex items-center justify-between mb-6 px-2">
                <h2 class="text-xl md:text-2xl font-bold text-text-primary tracking-tight">Recent Activity</h2>
                <button onclick="markAllAsRead()" class="text-primary-500 text-xs font-bold hover:text-primary-400 transition-colors uppercase tracking-wide">Mark all as read</button>
            </div>

            <div id="notificationsList" class="space-y-1">
                <!-- Loading Shimmer -->
                @for ($i = 1; $i <= 5; $i++)
                <div class="flex items-start space-x-4 p-4 rounded-2xl animate-pulse">
                    <div class="w-12 h-12 bg-bg-tertiary rounded-2xl"></div>
                    <div class="flex-1 space-y-2"><div class="h-3 bg-bg-tertiary rounded w-1/2"></div><div class="h-2 bg-bg-tertiary rounded w-1/4"></div></div>
                </div>
                @endfor
            </div>
            
            <div id="notificationsEmpty" class="hidden text-center py-20 animate-fade-in">
                <div class="text-4xl mb-4 opacity-50">🔔</div>
                <p class="text-text-secondary font-medium">Your neural uplink is quiet.</p>
                <p class="text-xs text-text-tertiary mt-1">No new signals detected.</p>
            </div>
        </div>

        <!-- Requests View -->
        <div id="viewRequests" class="section-view hidden transition-opacity duration-300 opacity-0">
             <div class="flex items-center justify-between mb-6 px-2">
                <h2 class="text-xl md:text-2xl font-bold text-text-primary tracking-tight">Pending Approval</h2>
                <button onclick="loadRequests()" class="text-text-tertiary hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>

            <div id="requestsList" class="space-y-3">
                 <!-- Loading Shimmer -->
                 @for ($i = 1; $i <= 3; $i++)
                 <div class="flex items-center justify-between p-4 rounded-2xl bg-bg-secondary border border-border-light animate-pulse">
                     <div class="flex items-center space-x-4">
                         <div class="w-12 h-12 bg-bg-tertiary rounded-full"></div>
                         <div class="space-y-2">
                             <div class="h-3 bg-bg-tertiary rounded w-24"></div>
                             <div class="h-2 bg-bg-tertiary rounded w-16"></div>
                         </div>
                     </div>
                 </div>
                 @endfor
            </div>

            <div id="requestsEmpty" class="hidden text-center py-20 animate-fade-in">
                <div class="text-4xl mb-4 opacity-50">🤝</div>
                <p class="text-text-secondary font-medium">No pending requests</p>
                <p class="text-xs text-text-tertiary mt-1">When someone requests to follow you, they'll appear here.</p>
            </div>
        </div>
    </main>

    <aside class="hidden xl:flex flex-col w-80 h-screen sticky top-0 px-6 py-8">
        <div class="bg-bg-secondary/50 backdrop-blur-md rounded-3xl border border-border-light p-6">
            <h3 class="font-bold text-text-primary mb-2 text-sm uppercase tracking-widest">Signal Control</h3>
            <p class="text-xs text-text-tertiary leading-relaxed">
                Manage your incoming neural transmissions. Requests allow you to filter who connects to your stream.
            </p>
        </div>
    </aside>
</div>

<script>
    let currentTab = 'notifications';

    document.addEventListener('DOMContentLoaded', () => {
        // Check URL param for tab
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'requests') {
            switchTab('requests');
        } else {
            loadNotifications();
            // Preload count for badge
            checkRequestCount();
        }
    });

    function switchTab(tab) {
        currentTab = tab;
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);

        // UI Updates
        const tabNotif = document.getElementById('tabNotifications');
        const tabReq = document.getElementById('tabRequests');
        const viewNotif = document.getElementById('viewNotifications');
        const viewReq = document.getElementById('viewRequests');
        
        // Indicators
        const indNotif = document.getElementById('indicatorNotifications');
        const indReq = document.getElementById('indicatorRequests');

        if (tab === 'notifications') {
            // Activate Notifications
            tabNotif.classList.remove('text-text-tertiary');
            tabNotif.classList.add('text-text-primary');
            indNotif.classList.remove('scale-x-0');
            
            // Deactivate Requests
            tabReq.classList.add('text-text-tertiary');
            tabReq.classList.remove('text-text-primary');
            indReq.classList.add('scale-x-0');

            // Views
            viewNotif.classList.remove('hidden');
            setTimeout(() => viewNotif.classList.remove('opacity-0'), 10);
            viewReq.classList.add('opacity-0');
            setTimeout(() => viewReq.classList.add('hidden'), 300);

            loadNotifications();
        } else {
            // Activate Requests
            tabReq.classList.remove('text-text-tertiary');
            tabReq.classList.add('text-text-primary');
            indReq.classList.remove('scale-x-0');

            // Deactivate Notifications
            tabNotif.classList.add('text-text-tertiary');
            tabNotif.classList.remove('text-text-primary');
            indNotif.classList.add('scale-x-0');

            // Views
            viewReq.classList.remove('hidden');
            setTimeout(() => viewReq.classList.remove('opacity-0'), 10);
            viewNotif.classList.add('opacity-0');
            setTimeout(() => viewNotif.classList.add('hidden'), 300);

            loadRequests();
        }
    }

    // --- NOTIFICATIONS LOGIC ---
    async function loadNotifications() {
        try {
            const response = await window.bridge.request('/notifications');
            let notifications = response.data || response || [];
            if (Array.isArray(notifications)) renderNotifications(notifications);
            else if (notifications.data && Array.isArray(notifications.data)) renderNotifications(notifications.data);
            
            if (!notifications || notifications.length === 0) {
                document.getElementById('notificationsEmpty').classList.remove('hidden');
                document.getElementById('notificationsList').classList.add('hidden');
            } else {
                document.getElementById('notificationsEmpty').classList.add('hidden');
                document.getElementById('notificationsList').classList.remove('hidden');
            }
        } catch (err) {
            console.error(err);
        }
    }

    function renderNotifications(notifications) {
        const container = document.getElementById('notificationsList');
        container.innerHTML = '';
        
        notifications.forEach(n => {
            const data = n.data || {};
            const isRead = n.read_at !== null;
            const item = document.createElement('div');
            
            const types = {
                'like': { icon: '❤️', label: 'liked your post', color: 'bg-accent-500/10 text-accent-500' },
                'comment': { icon: '💬', label: 'sent a signal', color: 'bg-primary-500/10 text-primary-500' },
                'follow': { icon: '👤', label: 'started following you', color: 'bg-blue-500/10 text-blue-500' },
                'follow_request': { icon: '🔒', label: 'requested entry', color: 'bg-purple-500/10 text-purple-500' },
                'mention': { icon: '✨', label: 'mentioned you', color: 'bg-yellow-500/10 text-yellow-500' }
            };
            const config = types[n.type] || { icon: '🔔', label: data.message || 'notification', color: 'bg-white/10 text-white' };
            const senderName = data.sender_name || data.username || 'System';
            const avatar = data.sender_avatar || `https://ui-avatars.com/api/?name=${senderName}`;
            
            // Client-side time calculation if server doesn't provide it
            let timeAgo = n.created_at_human;
            if (!timeAgo && n.created_at) {
                const date = new Date(n.created_at);
                const seconds = Math.floor((new Date() - date) / 1000);
                if (seconds < 60) timeAgo = 'Just now';
                else if (seconds < 3600) timeAgo = `${Math.floor(seconds / 60)}m ago`;
                else if (seconds < 86400) timeAgo = `${Math.floor(seconds / 3600)}h ago`;
                else timeAgo = `${Math.floor(seconds / 86400)}d ago`;
            } else if (!timeAgo) {
                timeAgo = 'Just now';
            }

            item.className = `group flex items-center space-x-4 p-4 rounded-3xl transition-all cursor-pointer border border-transparent hover:border-border-light hover:bg-bg-secondary/40 ${!isRead ? 'bg-primary-500/[0.03]' : ''}`;
            
            item.innerHTML = `
                <div class="relative flex-shrink-0">
                    <img src="${avatar}" class="w-12 h-12 rounded-full object-cover border border-border-light group-hover:border-primary-500/30 transition-colors">
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 ${config.color} rounded-full border border-bg-primary flex items-center justify-center text-[10px]">
                        ${config.icon}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                         <p class="text-sm text-text-primary truncate pr-2">
                            <span class="font-bold hover:text-primary-500 transition-colors">${senderName}</span>
                            <span class="text-text-secondary opacity-80">${config.label}</span>
                        </p>
                        ${!isRead ? '<div class="w-1.5 h-1.5 bg-primary-500 rounded-full flex-shrink-0"></div>' : ''}
                    </div>
                    <p class="text-[10px] text-text-tertiary mt-0.5 font-medium tracking-wide opacity-60">${timeAgo}</p>
                </div>
            `;
            
            item.onclick = () => {
                markRead(n.id);
                if (n.type === 'follow_request') {
                    switchTab('requests');
                } else if (n.type === 'follow' || n.type === 'follow_accept') {
                    const u = data.username || data.sender_name;
                    if(u) window.location.href = `/profile/${u}`;
                } else if (data.post_id) {
                     // Updated to use comments sheet instead of modal if desired, or explore
                     window.location.href = `/explore?post_id=${data.post_id}`; // Explore will handle regular render, user removed modal.
                     // Actually better logic: if we are handling explore as a redirect, that's fine.
                     // But user deleted modal. Explore might need check.
                     // Since explore page exists (grid), it's safe.
                }
            };
            container.appendChild(item);
        });
    }

    async function markRead(id) {
        try { await window.bridge.request(`/notifications/${id}/read`, { method: 'PATCH' }); loadNotifications(); } catch (e) {}
    }
    async function markAllAsRead() {
        try { 
            await window.bridge.request('/notifications/read-all', { method: 'POST' }); 
            window.toast('All clear', 'success'); 
            loadNotifications(); 
        } catch (e) {}
    }

    // --- REQUESTS LOGIC ---
    async function checkRequestCount() {
        try {
            const res = await window.bridge.request('/follow-requests');
            const requests = res.data.data || res.data || [];
            const count = requests.length;
            const badge = document.getElementById('requestsBadge');
            if (count > 0) {
                badge.innerText = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (e) {}
    }

    async function loadRequests() {
        try {
            const res = await window.bridge.request('/follow-requests');
            const requests = res.data.data || res.data || [];
            
            if (requests.length === 0) {
                document.getElementById('requestsEmpty').classList.remove('hidden');
                document.getElementById('requestsList').classList.add('hidden');
            } else {
                document.getElementById('requestsEmpty').classList.add('hidden');
                document.getElementById('requestsList').classList.remove('hidden');
                renderRequests(requests);
            }
            checkRequestCount(); // Update badge
        } catch (err) {
            console.error('Requests error', err);
        }
    }

    function renderRequests(requests) {
        const container = document.getElementById('requestsList');
        container.innerHTML = '';
        requests.forEach(user => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between p-4 rounded-3xl bg-bg-secondary/50 border border-border-light hover:border-primary-500/20 transition-all group';
            item.innerHTML = `
                <div class="flex items-center space-x-4 cursor-pointer" onclick="window.location.href='/profile/${user.username}'">
                    <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" 
                         class="w-12 h-12 rounded-full border border-border-light object-cover">
                    <div>
                        <h3 class="text-text-primary font-bold text-sm group-hover:text-primary-500 transition-colors">@${user.username}</h3>
                        <p class="text-text-tertiary text-xs mt-0.5">${user.display_name || user.username}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="handleRequest(${user.id}, 'accept', this)" 
                            class="px-4 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-primary-500/20">
                        Confirm
                    </button>
                    <button onclick="handleRequest(${user.id}, 'decline', this)" 
                            class="px-4 py-1.5 bg-white/5 hover:bg-red-500/10 hover:text-red-500 text-text-secondary text-xs font-bold rounded-xl transition-all">
                        Remove
                    </button>
                </div>
            `;
            container.appendChild(item);
        });
    }

    async function handleRequest(userId, action, btn) {
        const card = btn.closest('.flex.justify-between'); // Adjust selector if needed
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = '...';
        
        try {
            await window.bridge.request(`/users/${userId}/${action}-request`, { method: 'POST' });
            
            // Animate removal
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    card.remove();
                    checkRequestCount();
                    // Check empty
                    if (document.getElementById('requestsList').children.length === 0) {
                         document.getElementById('requestsEmpty').classList.remove('hidden');
                         document.getElementById('requestsList').classList.add('hidden');
                    }
                }, 300);
            }
        } catch (err) {
            btn.innerText = originalText;
            btn.disabled = false;
            window.toast('Action failed', 'error');
        }
    }
</script>
@endsection
