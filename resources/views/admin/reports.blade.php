@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-bg-primary text-text-primary p-6 md:p-12">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">
                    Moderation Center
                </h1>
                <p class="text-text-secondary mt-1">Review and resolve community reports to maintain network integrity.</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="bg-bg-secondary px-4 py-2 rounded-xl border border-border-light flex items-center space-x-3 shadow-md">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-sm font-bold text-text-primary" id="systemStatus">System Nominal</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border-light shadow-sm">
                <p class="text-xs font-bold text-text-tertiary uppercase tracking-wider">Pending Reports</p>
                <p class="text-3xl font-black text-text-primary mt-2" id="stat-pending">-</p>
            </div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border-light shadow-sm">
                <p class="text-xs font-bold text-text-tertiary uppercase tracking-wider">Reports Today</p>
                <p class="text-3xl font-black text-text-primary mt-2" id="stat-today">-</p>
            </div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border-light shadow-sm">
                <p class="text-xs font-bold text-text-tertiary uppercase tracking-wider">Total Users</p>
                <p class="text-3xl font-black text-text-primary mt-2" id="stat-users">-</p>
            </div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border-light shadow-sm">
                <p class="text-xs font-bold text-text-tertiary uppercase tracking-wider">Total Posts</p>
                <p class="text-3xl font-black text-text-primary mt-2" id="stat-posts">-</p>
            </div>
        </div>

        <!-- Reports List -->
        <div class="bg-bg-secondary rounded-3xl border border-border-light shadow-xl overflow-hidden min-h-[500px] flex flex-col">
            <!-- Toolbar -->
            <div class="p-6 border-b border-border-light flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-primary">Incoming Reports</h2>
                <button onclick="loadReports()" class="p-2 hover:bg-bg-tertiary rounded-full transition-colors text-text-secondary" title="Refresh">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
            
            <!-- List Content -->
            <div id="reportsList" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Skeleton Loader -->
                <div class="animate-pulse space-y-3">
                    <div class="h-20 bg-bg-tertiary rounded-xl w-full"></div>
                    <div class="h-20 bg-bg-tertiary rounded-xl w-full"></div>
                    <div class="h-20 bg-bg-tertiary rounded-xl w-full"></div>
                </div>
            </div>
            
             <!-- Pagination -->
            <div class="p-4 border-t border-border-light flex justify-between items-center bg-bg-tertiary/20">
                <button id="prevBtn" class="px-4 py-2 rounded-lg text-sm font-bold text-text-secondary disabled:opacity-50 hover:bg-bg-tertiary">Previous</button>
                <span class="text-sm text-text-secondary" id="pageInfo">Page 1</span>
                <button id="nextBtn" class="px-4 py-2 rounded-lg text-sm font-bold text-text-secondary disabled:opacity-50 hover:bg-bg-tertiary">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Resolution Modal -->
<div id="resolutionModal" class="fixed inset-0 z-[100] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-bg-secondary rounded-3xl w-full max-w-2xl shadow-2xl border border-border-light anim-scale-in flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-border-light flex justify-between items-center">
             <h3 class="text-xl font-bold text-text-primary">Resolve Report #<span id="modalReportId"></span></h3>
             <button onclick="closeResolutionModal()" class="text-text-secondary hover:text-text-primary">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Report Details -->
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                     <p class="text-text-tertiary uppercase text-xs font-bold mb-1">Reporter</p>
                     <p class="text-text-primary font-medium" id="modalReporter">@username</p>
                </div>
                <div>
                     <p class="text-text-tertiary uppercase text-xs font-bold mb-1">Reason</p>
                     <span class="px-2 py-1 bg-red-500/10 text-red-500 rounded text-xs font-bold" id="modalReason">Spam</span>
                </div>
                <div class="col-span-2">
                     <p class="text-text-tertiary uppercase text-xs font-bold mb-1">Details</p>
                     <p class="text-text-secondary bg-bg-tertiary p-3 rounded-lg" id="modalDetails">No details provided.</p>
                </div>
            </div>
            
            <!-- Content Preview -->
            <div>
                <p class="text-text-tertiary uppercase text-xs font-bold mb-2">Reported Content</p>
                <div id="modalContentPreview" class="border border-border-light rounded-xl p-4 bg-bg-tertiary/30">
                    <!-- Dynamic preview loaded here -->
                </div>
            </div>
            
            <!-- Action Form -->
            <div>
                <p class="text-text-tertiary uppercase text-xs font-bold mb-2">Action</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="resolutionAction" value="dismiss" class="peer sr-only" checked>
                        <div class="p-3 rounded-xl border border-border-light text-center transition-all peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500 hover:bg-bg-tertiary">
                            <span class="font-bold text-sm">Dismiss</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="resolutionAction" value="warn" class="peer sr-only">
                        <div class="p-3 rounded-xl border border-border-light text-center transition-all peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-500 hover:bg-bg-tertiary">
                            <span class="font-bold text-sm">Warn User</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="resolutionAction" value="delete" class="peer sr-only">
                        <div class="p-3 rounded-xl border border-border-light text-center transition-all peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 hover:bg-bg-tertiary">
                            <span class="font-bold text-sm">Delete Content</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="resolutionAction" value="suspend" class="peer sr-only">
                        <div class="p-3 rounded-xl border border-border-light text-center transition-all peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 hover:bg-bg-tertiary">
                            <span class="font-bold text-sm">Suspend User</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div>
                <p class="text-text-tertiary uppercase text-xs font-bold mb-2">Internal Notes</p>
                <textarea id="resolutionNotes" rows="2" class="w-full bg-bg-tertiary border border-border-light rounded-xl px-4 py-3 text-text-primary focus:border-primary-500 outline-none transition-colors text-sm"></textarea>
            </div>
        </div>
        
        <div class="p-6 border-t border-border-light flex justify-end space-x-3 bg-bg-secondary rounded-b-3xl">
            <button onclick="closeResolutionModal()" class="px-5 py-2 rounded-xl font-bold text-text-secondary hover:bg-bg-tertiary transition-colors">Cancel</button>
            <button onclick="submitResolution()" class="px-5 py-2 bg-text-primary text-bg-primary hover:opacity-90 rounded-xl font-bold transition-transform active:scale-95 shadow-lg">Confirm Resolution</button>
        </div>
    </div>
</div>

<script>
    let currentReports = [];
    let currentPage = 1;
    let currentReportId = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();
        loadReports();
    });

    async function loadStats() {
        try {
            const stats = await window.bridge.request('/admin/stats');
            document.getElementById('stat-pending').innerText = stats.pending_reports;
            document.getElementById('stat-today').innerText = stats.reports_today;
            document.getElementById('stat-users').innerText = stats.users;
            document.getElementById('stat-posts').innerText = stats.posts;
        } catch (err) {
            console.error('Stats failed', err);
        }
    }

    async function loadReports(page = 1) {
        const list = document.getElementById('reportsList');
        // list.innerHTML = '<div class="text-center py-10"><div class="animate-spin w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full mx-auto"></div></div>';
        
        try {
            const data = await window.bridge.request(`/admin/reports?page=${page}`);
            currentReports = data.data; // Store for modal
            currentPage = data.current_page;
            
            renderReports(data.data);
            
            // Pagination controls
            document.getElementById('pageInfo').innerText = `Page ${data.current_page} of ${data.last_page}`;
            document.getElementById('prevBtn').disabled = !data.prev_page_url;
            document.getElementById('nextBtn').disabled = !data.next_page_url;
            
            document.getElementById('prevBtn').onclick = () => loadReports(page - 1);
            document.getElementById('nextBtn').onclick = () => loadReports(page + 1);
            
        } catch (err) {
            console.error('Reports load failed', err);
            list.innerHTML = `<div class="text-center py-10 text-red-500">Failed to load reports. ${err.message || ''}</div>`;
        }
    }

    function renderReports(reports) {
        const list = document.getElementById('reportsList');
        if (!reports || reports.length === 0) {
            list.innerHTML = `<div class="flex flex-col items-center justify-center h-64 text-text-tertiary">
                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-bold">All clear!</p>
                <p class="text-sm">No pending reports found.</p>
            </div>`;
            return;
        }

        list.innerHTML = reports.map(r => `
            <div class="bg-bg-primary/50 p-4 rounded-xl border border-border-light flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:bg-bg-tertiary/30">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-text-primary text-sm uppercase tracking-wide">${r.reportable_type.split('\\\\').pop()} Report</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${getStatusColor(r.status)}">${r.status}</span>
                        </div>
                        <p class="text-text-secondary text-sm mt-1">
                            <span class="text-red-400 font-bold">${r.reason}</span> • Reported by @${r.reporter?.username || 'Unknown'}
                        </p>
                        <p class="text-text-tertiary text-xs mt-1">${r.details || 'No additional details.'}</p>
                    </div>
                </div>
                <div>
                     <button onclick="openResolutionModal(${r.id})" class="w-full md:w-auto px-4 py-2 bg-bg-secondary hover:bg-bg-tertiary border border-border-light rounded-lg text-sm font-bold text-text-primary transition-colors cursor-pointer shadow-sm">
                        Review & Resolve
                     </button>
                </div>
            </div>
        `).join('');
    }

    function getStatusColor(status) {
        switch(status) {
            case 'pending': return 'bg-yellow-500/20 text-yellow-500';
            case 'resolved': return 'bg-green-500/20 text-green-500';
            case 'dismissed': return 'bg-gray-500/20 text-gray-400';
            default: return 'bg-blue-500/20 text-blue-500';
        }
    }

    function openResolutionModal(id) {
        const report = currentReports.find(r => r.id === id);
        if (!report) return;

        currentReportId = id;
        document.getElementById('modalReportId').innerText = id;
        document.getElementById('modalReporter').innerText = '@' + (report.reporter?.username || 'Unknown');
        document.getElementById('modalReason').innerText = report.reason;
        document.getElementById('modalDetails').innerText = report.details || 'No details provided.';
        document.getElementById('resolutionNotes').value = '';

        // Render target content preview safely
        const preview = document.getElementById('modalContentPreview');
        if (report.reportable && report.reportable_type.includes('Post')) {
            const post = report.reportable;
            const caption = post.caption ? `<p class="italic text-text-secondary mb-2">"${post.caption}"</p>` : '';
            preview.innerHTML = `
                ${caption}
                <p class="text-xs text-text-tertiary">Post ID: ${post.id} • User ID: ${post.user_id}</p>
            `;
        } else if (report.reportable && report.reportable_type.includes('User')) {
            const user = report.reportable;
            preview.innerHTML = `
                <div class="flex items-center space-x-3">
                     <img src="${user.avatar_url || 'https://ui-avatars.com/api/?name='+user.username}" class="w-10 h-10 rounded-full">
                     <div>
                         <p class="font-bold text-text-primary">${user.username}</p>
                         <p class="text-xs text-text-tertiary">${user.email}</p>
                     </div>
                </div>
            `;
        } else {
             preview.innerHTML = `<p class="text-text-secondary italic">Content unavailable (might be deleted).</p>`;
        }

        document.getElementById('resolutionModal').classList.remove('hidden');
    }

    function closeResolutionModal() {
        document.getElementById('resolutionModal').classList.add('hidden');
    }

    async function submitResolution() {
        if (!currentReportId) return;
        
        const action = document.querySelector('input[name="resolutionAction"]:checked').value;
        const notes = document.getElementById('resolutionNotes').value;
        
        try {
            await window.bridge.request(`/admin/reports/${currentReportId}`, {
                method: 'PATCH',
                body: JSON.stringify({ action, notes })
            });
            
            window.toast('Report resolved successfully', 'success');
            closeResolutionModal();
            loadReports(currentPage); // Refresh list
            loadStats();
        } catch (err) {
            console.error(err);
            window.toast('Failed to resolve report', 'error');
        }
    }
</script>
@endsection
