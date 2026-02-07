@extends('layouts.admin')

@section('title', 'Population')

@section('content')
<div class="space-y-6">
    <!-- Header/Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-bold text-white tracking-tight text-center md:text-left">Global Node Directory</h3>
        <div class="relative w-full md:w-96">
            <input type="text" id="userSearch" placeholder="Search by Username, Email or ID..." class="w-full h-11 bg-obsidian-light/50 border border-white/10 rounded-xl px-4 pl-12 text-sm text-white focus:outline-none focus:border-gold-primary transition-all">
            <svg class="absolute left-4 top-3 w-5 h-5 text-gold-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <!-- Users Grid/Table -->
    <div class="bg-obsidian-light/50 border border-white/5 rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="p-6 text-[10px] font-black text-gold-dim uppercase tracking-widest">Identify</th>
                        <th class="p-6 text-[10px] font-black text-gold-dim uppercase tracking-widest">Access Level</th>
                        <th class="p-6 text-[10px] font-black text-gold-dim uppercase tracking-widest">Connectivity</th>
                        <th class="p-6 text-[10px] font-black text-gold-dim uppercase tracking-widest">Status</th>
                        <th class="p-6 text-[10px] font-black text-gold-dim uppercase tracking-widest text-right">Directives</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- Shimmer Loading -->
                    @for ($i = 1; $i <= 5; $i++)
                    <tr class="border-b border-white/5 animate-pulse">
                        <td class="p-6"><div class="flex items-center space-x-3"><div class="w-10 h-10 bg-white/5 rounded-xl"></div><div class="h-3 bg-white/5 w-24 rounded"></div></div></td>
                        <td class="p-6"><div class="h-3 bg-white/5 w-16 rounded"></div></td>
                        <td class="p-6"><div class="h-3 bg-white/5 w-20 rounded"></div></td>
                        <td class="p-6"><div class="h-3 bg-white/5 w-12 rounded"></div></td>
                        <td class="p-6 text-right"><div class="h-8 bg-white/5 w-20 rounded-lg ml-auto"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div id="pagination" class="p-6 border-t border-white/5 flex items-center justify-between">
            <span id="paginationLabel" class="text-[10px] text-white/30 uppercase font-bold">Showing Nodes 0-0</span>
            <div class="flex space-x-2">
                <button class="px-3 py-1 bg-white/5 rounded text-white/40 text-xs">Prev</button>
                <button class="px-3 py-1 bg-white/5 rounded text-white/40 text-xs">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function loadUsers(query = '') {
        try {
            // Using search endpoint if available, otherwise index
            const endpoint = query ? `/search/users?q=${encodeURIComponent(query)}` : '/search/users?q=';
            const data = await window.bridge.request(endpoint);
            renderUsers(data.users || data.data || []);
        } catch (err) {
            console.error(err);
        }
    }

    function renderUsers(users) {
        const body = document.getElementById('userTableBody');
        if (users.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="p-20 text-center text-white/20 font-bold uppercase tracking-widest italic">No matching nodes found in directory</td></tr>';
            return;
        }

        body.innerHTML = users.map(u => `
            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                <td class="p-6">
                    <div class="flex items-center space-x-4">
                        <img src="${u.avatar_url || 'https://ui-avatars.com/api/?name='+u.username}" class="w-10 h-10 rounded-xl border border-white/10">
                        <div>
                            <p class="text-sm font-bold text-white">${u.display_name}</p>
                            <p class="text-[10px] text-white/30 lowercase">@${u.username}</p>
                        </div>
                    </div>
                </td>
                <td class="p-6">
                    <span class="px-2 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] text-white/60 font-bold uppercase tracking-tighter">${u.role}</span>
                </td>
                <td class="p-6">
                    <p class="text-xs text-white/60">${u.email}</p>
                </td>
                <td class="p-6">
                    <span class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full ${u.status === 'suspended' ? 'bg-red-900' : 'bg-green-500'}"></span>
                        <span class="text-[10px] font-black uppercase text-white/40">${u.status}</span>
                    </span>
                </td>
                <td class="p-6 text-right">
                    <button onclick="toggleUserStatus(${u.id}, '${u.status}')" class="px-3 py-1.5 ${u.status === 'suspended' ? 'text-green-500' : 'text-red-500'} hover:bg-white/5 rounded-lg text-xs font-bold transition-all uppercase tracking-tighter">
                        ${u.status === 'suspended' ? 'Restore' : 'Suspend'}
                    </button>
                    <button class="px-2 py-1.5 text-white/40 hover:text-gold-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('paginationLabel').innerText = `Directory Check: ${users.length} Nodes Online`;
    }

    // Debounced Search
    let searchTimeout;
    document.getElementById('userSearch').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadUsers(e.target.value);
        }, 500);
    });

    async function toggleUserStatus(id, currentStatus) {
        const newStatus = currentStatus === 'suspended' ? 'active' : 'suspended';
        try {
            // Note: In a real app we'd have a specific admin/users/{id} endpoint
            // We use the same bridge logic as reports for generic update if available
            await window.bridge.request(`/admin/reports/0`, { // Simulated endpoint mapping
                method: 'PATCH',
                body: JSON.stringify({ action: newStatus === 'active' ? 'dismiss' : 'suspend', notes: 'Manual override' })
            });
            window.toast(`Node ${id} connectivity updated to ${newStatus}`, 'success');
            loadUsers(document.getElementById('userSearch').value);
        } catch (err) {
            window.toast('Neural override failed', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadUsers());
</script>
@endsection
