@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 pb-24">
    <!-- Header -->
    <div class="mb-8 animate-slide-in">
        <h1 class="text-3xl font-black text-text-primary tracking-tighter uppercase">Command Center</h1>
        <p class="text-sm text-text-secondary">Configure your neural presence and platform protocols.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <div class="w-full md:w-64 space-y-2 animate-fade-in" style="animation-delay: 100ms">
            <button onclick="switchTab('account')" class="tab-btn active w-full flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Neural Account</span>
            </button>
            <button onclick="switchTab('privacy')" class="tab-btn w-full flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Privacy Protocols</span>
            </button>
            <button onclick="switchTab('appearance')" class="tab-btn w-full flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a2 2 0 112.828 2.828l-1.172 1.172"/></svg>
                <span>Visual Interface</span>
            </button>
        </div>

        <!-- Content Area -->
        <div class="flex-1 glass p-8 rounded-[2.5rem] shadow-xl animate-fade-in" style="animation-delay: 200ms">
            <!-- Account Tab -->
            <div id="accountTab" class="settings-tab space-y-8">
                <div>
                    <h3 class="text-xl font-bold text-text-primary">Profile Identity</h3>
                    <p class="text-xs text-text-tertiary mt-1">Management of your public-facing data streams.</p>
                </div>
                
                <form id="profileSettingsForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Display Name</label>
                            <input type="text" id="setDisplayName" class="w-full h-12 bg-bg-secondary border-2 border-border-light rounded-2xl px-4 text-sm focus:border-primary-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Username</label>
                            <input type="text" id="setUsername" class="w-full h-12 bg-bg-secondary border-2 border-border-light rounded-2xl px-4 text-sm focus:border-primary-500 transition-all opacity-50 cursor-not-allowed" readonly disabled>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-secondary uppercase tracking-widest pl-1">Bio Transmission</label>
                        <textarea id="setBio" rows="3" class="w-full bg-bg-secondary border-2 border-border-light rounded-2xl p-4 text-sm focus:border-primary-500 transition-all resize-none"></textarea>
                    </div>
                    <button type="submit" class="h-12 bg-primary-500 text-white px-8 rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-primary-500/20 transition-all">UPDATE IDENTITY</button>
                </form>
            </div>

            <!-- Privacy Tab -->
            <div id="privacyTab" class="settings-tab hidden space-y-8">
                <div>
                    <h3 class="text-xl font-bold text-text-primary">Safety Protocols</h3>
                    <p class="text-xs text-text-tertiary mt-1">Control who can interact with your neural node.</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-bg-secondary/50 rounded-2xl border border-border-light">
                        <div>
                            <p class="text-sm font-bold text-text-primary">Incognito Node</p>
                            <p class="text-[10px] text-text-secondary tracking-tight">Only authorized followers can view your transmissions.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="privateProfile" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Appearance Tab -->
            <div id="appearanceTab" class="settings-tab hidden space-y-8">
                <div>
                    <h3 class="text-xl font-bold text-text-primary">Visual Overlay</h3>
                    <p class="text-xs text-text-tertiary mt-1">Adjust the neural interface to your preference.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button id="theme-btn-light" onclick="setTheme('light')" class="p-6 rounded-3xl border-2 border-border-light hover:border-primary-500 transition-all text-center space-y-3">
                        <div class="w-12 h-12 bg-white rounded-full mx-auto border shadow-sm"></div>
                        <span class="text-xs font-bold uppercase tracking-widest text-text-primary">Solar (Light)</span>
                    </button>
                    <button id="theme-btn-dark" onclick="setTheme('dark')" class="p-6 rounded-3xl border-2 border-primary-500 bg-bg-secondary transition-all text-center space-y-3">
                        <div class="w-12 h-12 bg-gray-900 rounded-full mx-auto border border-white/10 shadow-sm"></div>
                        <span class="text-xs font-bold uppercase tracking-widest text-text-primary">Void (Dark)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tab-btn.active {
        background: var(--primary-500);
        color: white !important;
        box-shadow: 0 10px 20px -5px rgba(45, 63, 230, 0.3);
    }
</style>

<script>
    function switchTab(tab) {
        // Toggle Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-text-secondary', 'font-medium');
        });
        event.currentTarget.classList.add('active');
        event.currentTarget.classList.remove('text-text-secondary', 'font-medium');

        // Toggle Views
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.add('hidden'));
        document.getElementById(tab + 'Tab').classList.remove('hidden');
    }

    async function loadSettings() {
        if (!window.currentUser) {
            await initUser();
        }
        const user = window.currentUser;
        document.getElementById('setDisplayName').value = user.display_name;
        document.getElementById('setUsername').value = `@${user.username}`;
        document.getElementById('setBio').value = user.bio || '';
        document.getElementById('privateProfile').checked = user.is_private;
    }

    document.getElementById('profileSettingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const display_name = document.getElementById('setDisplayName').value;
        const bio = document.getElementById('setBio').value;

        try {
            await window.bridge.request('/profile', {
                method: 'PATCH',
                body: JSON.stringify({ display_name, bio })
            });
            window.toast('Neural profile updated!', 'success');
            // Refresh local state
            initUser();
        } catch (err) {
            window.toast('Transmission failed', 'error');
        }
    });

    document.getElementById('privateProfile').onchange = async (e) => {
        try {
            await window.bridge.request('/profile', {
                method: 'PATCH',
                body: JSON.stringify({ is_private: e.target.checked })
            });
            window.toast('Privacy protocols updated', 'info');
        } catch (err) {}
    };

    function setTheme(theme) {
        if (window.NeuralTheme && typeof window.NeuralTheme.set === 'function') {
            window.NeuralTheme.set(theme);
        } else {
            // Fallback
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        updateThemeButtons();
        window.toast(`Interface set to ${theme}`, 'info');
    }

    function updateThemeButtons() {
        const isDark = document.documentElement.classList.contains('dark');
        const darkBtn = document.getElementById('theme-btn-dark');
        const lightBtn = document.getElementById('theme-btn-light');

        if (isDark) {
            darkBtn.className = "p-6 rounded-3xl border-2 border-primary-500 bg-bg-secondary transition-all text-center space-y-3";
            lightBtn.className = "p-6 rounded-3xl border-2 border-border-light hover:border-primary-500 transition-all text-center space-y-3";
        } else {
            lightBtn.className = "p-6 rounded-3xl border-2 border-primary-500 bg-bg-secondary transition-all text-center space-y-3";
            darkBtn.className = "p-6 rounded-3xl border-2 border-border-light hover:border-primary-500 transition-all text-center space-y-3";
        }
    }

    // Listen for theme changes from other components (like sidebar)
    window.addEventListener('theme-changed', (e) => {
        updateThemeButtons();
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadSettings();
        updateThemeButtons();
    });
</script>
@endsection
