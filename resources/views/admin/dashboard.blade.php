@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="space-y-8">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-obsidian-light/50 border border-white/5 p-6 rounded-2xl">
            <p class="text-[10px] text-gold-dim uppercase tracking-widest font-bold">Total Nexus Users</p>
            <div class="flex items-end justify-between mt-2">
                <h3 id="statUsers" class="text-3xl font-bold text-white">---</h3>
                <span class="text-green-500 text-xs font-bold">+12%</span>
            </div>
        </div>
        <div class="bg-obsidian-light/50 border border-white/5 p-6 rounded-2xl">
            <p class="text-[10px] text-gold-dim uppercase tracking-widest font-bold">Active Transmissions</p>
            <div class="flex items-end justify-between mt-2">
                <h3 id="statPosts" class="text-3xl font-bold text-white">---</h3>
                <span class="text-green-500 text-xs font-bold">+5%</span>
            </div>
        </div>
        <div class="bg-obsidian-light/50 border border-white/5 p-6 rounded-2xl">
            <p class="text-[10px] text-gold-dim uppercase tracking-widest font-bold">Pending Reports</p>
            <div class="flex items-end justify-between mt-2">
                <h3 id="statReports" class="text-3xl font-bold text-white">---</h3>
                <span id="reportStatus" class="text-red-500 text-xs font-bold">Critical</span>
            </div>
        </div>
        <div class="bg-obsidian-light/50 border border-white/5 p-6 rounded-2xl">
            <p class="text-[10px] text-gold-dim uppercase tracking-widest font-bold">Daily Signals</p>
            <div class="flex items-end justify-between mt-2">
                <h3 id="statDaily" class="text-3xl font-bold text-white">---</h3>
                <span class="text-gold-primary text-xs font-bold">Stable</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-obsidian-light/50 border border-white/5 rounded-3xl overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h4 class="font-bold text-white">System Logs</h4>
                    <button class="text-xs text-gold-primary font-bold">View All</button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-4 text-sm text-white/60">
                        <span class="w-2 h-2 bg-gold-primary rounded-full shadow-[0_0_8px_rgba(255,215,0,0.5)]"></span>
                        <p><span class="text-white">Admin Alpha</span> resolved report #1204 (Spam)</p>
                        <span class="text-[10px] ml-auto">2m ago</span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-white/60">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <p><span class="text-white">New Node</span> joined the network (@neo_matrix)</p>
                        <span class="text-[10px] ml-auto">5m ago</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-gold-primary/20 to-transparent border border-gold-primary/20 p-6 rounded-3xl">
                <h4 class="font-bold text-gold-primary mb-2">Neural Priority</h4>
                <p class="text-sm text-white/80 leading-relaxed">3 reports are approaching 24-hour resolution limit. Prompt action required.</p>
                <button onclick="window.location.href='/admin/reports'" class="mt-4 w-full py-3 bg-gold-primary text-black font-bold rounded-xl hover:bg-white transition-all">Moderate Now</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function loadStats() {
        try {
            const data = await window.bridge.request('/admin/stats');
            document.getElementById('statUsers').innerText = data.users;
            document.getElementById('statPosts').innerText = data.posts;
            document.getElementById('statReports').innerText = data.pending_reports;
            document.getElementById('statDaily').innerText = data.reports_today;

            if (data.pending_reports === 0) {
                const badge = document.getElementById('reportStatus');
                badge.innerText = 'Clear';
                badge.className = 'text-green-500 text-xs font-bold';
            }
        } catch (err) {
            console.error('Stats failed:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', loadStats);
</script>
@endsection
