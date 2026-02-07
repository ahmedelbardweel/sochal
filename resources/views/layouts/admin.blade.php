<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, viewport-fit=cover">
    <title>AbsScroll | Command Center</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            primary: '#FFD700',
                            dim: '#B8860B',
                        },
                        obsidian: {
                            DEFAULT: '#0A0A0B',
                            light: '#1A1A1C',
                        }
                    }
                }
            }
        }
    </script>
    <script src="{{ asset('js/bridge.js') }}" defer></script>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <style>
        :root {
            --gold-primary: #FFD700;
            --gold-dim: #B8860B;
            --obsidian: #0A0A0B;
            --obsidian-light: #1A1A1C;
        }
        body {
            background-color: var(--obsidian);
            color: #E2E8F0;
            font-family: 'Inter', sans-serif;
        }
        .admin-sidebar {
            background-color: var(--obsidian-light);
            border-right: 1px solid rgba(255, 215, 0, 0.1);
        }
        .nav-link.active {
            background: linear-gradient(to right, rgba(255, 215, 0, 0.1), transparent);
            border-left: 2px solid var(--gold-primary);
            color: var(--gold-primary);
        }
    </style>
</head>
<body class="antialiased h-screen overflow-hidden flex">
    
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar w-64 flex flex-col h-full flex-shrink-0">
        <div class="p-8 border-b border-white/5">
            <h1 class="text-xl font-bold tracking-tighter text-white uppercase italic">
                <span class="text-gold-primary">Abs</span>Command
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-2 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center space-x-3 p-3 rounded-xl hover:bg-white/5 transition-all {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="text-sm font-medium">Overview</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-link flex items-center space-x-3 p-3 rounded-xl hover:bg-white/5 transition-all {{ Request::routeIs('admin.reports') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-medium">Moderation</span>
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link flex items-center space-x-3 p-3 rounded-xl hover:bg-white/5 transition-all {{ Request::routeIs('admin.users') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-sm font-medium">Population</span>
            </a>
        </nav>

        <div class="p-6 border-t border-white/5">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-white/5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-sm font-medium">Return Home</span>
            </a>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col h-full bg-obsidian overflow-hidden">
        <header class="h-20 border-b border-white/5 flex items-center justify-between px-8 bg-obsidian-light/30 backdrop-blur-md">
            <div>
                <h2 class="text-xs font-bold text-gold-dim uppercase tracking-[0.2em]">Neural Network Oversight</h2>
                <div class="flex items-center text-sm space-x-2 text-white/50 mt-1">
                    <span>Command</span>
                    <span>/</span>
                    <span class="text-white">@yield('title', 'Overview')</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right">
                    <p id="adminName" class="text-sm font-bold text-white">Neural Operator</p>
                    <p id="adminRole" class="text-[10px] text-gold-primary uppercase tracking-tighter">Level 1 Admin</p>
                </div>
                <img id="adminAvatar" src="https://ui-avatars.com/api/?name=Admin&background=random" class="w-10 h-10 rounded-xl border border-white/10 ring-2 ring-gold-primary/20">
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    <!-- UI Feedback layer -->
    <div id="notifications" class="fixed bottom-4 right-4 z-[200] flex flex-col gap-2"></div>

    <script>
        // Use Global Init from app layout or redefined here for admin context
        async function initAdmin() {
            if (window.bridge.getToken()) {
                const data = await window.bridge.me();
                if (data.user.role !== 'admin' && data.user.role !== 'moderator') {
                    window.location.href = '/home';
                    return;
                }
                document.getElementById('adminName').innerText = data.user.display_name;
                document.getElementById('adminRole').innerText = `${data.user.role.toUpperCase()} OPERATOR`;
                document.getElementById('adminAvatar').src = data.user.avatar_url || `https://ui-avatars.com/api/?name=${data.user.username}`;
            } else {
                window.location.href = '/login';
            }
        }

        window.toast = function(message, type = 'info') {
            const container = document.getElementById('notifications');
            const toast = document.createElement('div');
            const colors = {
                success: 'border-yellow-500', 
                error: 'border-red-500',
                info: 'border-blue-500'
            };
            toast.className = `p-4 rounded-lg shadow-2xl border-l-4 ${colors[type]} bg-[#1A1A1C] text-white animate-slide-in mb-2 min-w-[300px]`;
            toast.innerHTML = `<p class="text-sm font-bold tracking-tight">${message}</p>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full', 'transition-all', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        document.addEventListener('DOMContentLoaded', initAdmin);
    </script>
    <script>
        // Prevent Zoom on iOS
        document.addEventListener('gesturestart', function(e) {
            e.preventDefault();
        });
        document.addEventListener('dblclick', function(e) {
            e.preventDefault();
        }, { passive: false });
    </script>
</body>
</html>
