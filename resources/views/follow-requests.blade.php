@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-bg-primary">
    @include('partials.sidebar')

    <main class="flex-1 max-w-2xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8 px-2">
            <h2 class="text-3xl font-bold tracking-tight text-text-primary">Follow Requests</h2>
            <span id="requestCount" class="text-sm text-text-secondary"></span>
        </div>

        <div id="requestsList" class="space-y-3">
            <!-- Loading Shimmer -->
            @for ($i = 1; $i <= 5; $i++)
            <div class="flex items-center justify-between p-4 rounded-2xl bg-bg-secondary border border-border-light animate-pulse">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-bg-tertiary rounded-full"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-bg-tertiary rounded w-32"></div>
                        <div class="h-3 bg-bg-tertiary rounded w-24"></div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="w-20 h-8 bg-bg-tertiary rounded-lg"></div>
                    <div class="w-20 h-8 bg-bg-tertiary rounded-lg"></div>
                </div>
            </div>
            @endfor
        </div>
        
        <div id="emptyState" class="hidden text-center py-20">
            <div class="text-6xl mb-4">🤝</div>
            <p class="text-text-secondary text-lg mb-2">No pending requests</p>
            <p class="text-text-tertiary text-sm">When someone requests to follow you, they'll appear here.</p>
        </div>
    </main>
</div>

<script>
    async function loadRequests() {
        try {
            const response = await window.bridge.request('/follow-requests');
            console.log('Follow Requests:', response);
            
            const requests = response.data.data || response.data || [];
            
            renderRequests(requests);
            
            // Update count
            const count = response.count || requests.length;
            document.getElementById('requestCount').innerText = count === 0 ? '' : `${count} pending`;
            
            if (requests.length === 0) {
                document.getElementById('emptyState').classList.remove('hidden');
                document.getElementById('requestsList').classList.add('hidden');
            }
        } catch (err) {
            console.error('Failed to load follow requests:', err);
            const container = document.getElementById('requestsList');
            container.innerHTML = `
                <div class="text-center py-20">
                    <div class="text-4xl mb-4">⚠️</div>
                    <p class="text-text-secondary mb-2">Failed to load requests</p>
                    <button onclick="loadRequests()" class="text-primary-500 text-sm font-bold hover:underline">Retry</button>
                </div>
            `;
            window.toast?.('Failed to load follow requests', 'error');
        }
    }

    function renderRequests(requests) {
        const container = document.getElementById('requestsList');
        container.innerHTML = '';

        requests.forEach(user => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between p-4 rounded-2xl bg-bg-secondary border border-border-light hover:border-primary-500/30 transition-all group';
            item.innerHTML = `
                <div class="flex items-center space-x-4 cursor-pointer" onclick="window.location.href='/profile/${user.username}'">
                    <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" 
                         class="w-14 h-14 rounded-full border-2 border-white/10 object-cover shadow-sm">
                    <div>
                        <h3 class="text-text-primary font-bold text-sm group-hover:text-primary-500 transition-colors">
                            @${user.username}
                            ${user.is_verified ? '<span class="text-xs bg-primary-500 text-white px-1.5 py-0.5 rounded ml-1">✓</span>' : ''}
                        </h3>
                        <p class="text-text-tertiary text-xs mt-1">${user.display_name || user.username}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button onclick="handleAccept(${user.id}, this)" 
                            class="accept-btn px-5 py-2 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/20 transition-all transform active:scale-95">
                        Accept
                    </button>
                    <button onclick="handleDecline(${user.id}, this)" 
                            class="decline-btn px-5 py-2 bg-bg-tertiary hover:bg-red-500/10 text-text-secondary hover:text-red-500 text-sm font-semibold rounded-xl border border-border-light hover:border-red-500/30 transition-all">
                        Decline
                    </button>
                </div>
            `;
            container.appendChild(item);
        });
    }

    async function handleAccept(userId, btn) {
        const card = btn.closest('.flex');
        btn.disabled = true;
        btn.innerText = 'Accepting...';
        
        try {
            await window.bridge.request(`/users/${userId}/accept-request`, { method: 'POST' });
            window.toast?.('Request accepted! 🎉', 'success');
            
            // Remove card with animation
            card.style.opacity = '0';
            card.style.transform = 'translateX(100px)';
            setTimeout(() => {
                card.remove();
                
                // Check if no more requests
                const remaining = document.querySelectorAll('#requestsList > div').length;
                if (remaining === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                    document.getElementById('requestsList').classList.add('hidden');
                }
            }, 300);
            
            loadRequests(); // Refresh page count
            if (window.refreshFollowRequestsBadge) window.refreshFollowRequestsBadge(); // Refresh sidebar badge
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerText = 'Accept';
            window.toast?.('Failed to accept request', 'error');
        }
    }

    async function handleDecline(userId, btn) {
        const card = btn.closest('.flex');
        btn.disabled = true;
        btn.innerText = 'Declining...';
        
        try {
            await window.bridge.request(`/users/${userId}/decline-request`, { method: 'POST' });
            window.toast?.('Request declined', 'info');
            
            // Remove card with animation
            card.style.opacity = '0';
            card.style.transform = 'translateX(-100px)';
            setTimeout(() => {
                card.remove();
                
                // Check if no more requests
                const remaining = document.querySelectorAll('#requestsList > div').length;
                if (remaining === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                    document.getElementById('requestsList').classList.add('hidden');
                }
            }, 300);
            
            loadRequests(); // Refresh page count
            if (window.refreshFollowRequestsBadge) window.refreshFollowRequestsBadge(); // Refresh sidebar badge
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.innerText = 'Decline';
            window.toast?.('Failed to decline request', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', loadRequests);
</script>

<style>
    .flex.group {
        transition: opacity 0.3s, transform 0.3s;
    }
</style>
@endsection
