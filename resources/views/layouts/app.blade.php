<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ asset('js/theme-switcher.js') }}"></script>

    <!-- GLOBAL ERROR TRAP -->
    <script>
        window.onerror = function(msg, url, line, col, error) {
            const container = document.getElementById('global-error-trap') || document.createElement('div');
            container.id = 'global-error-trap';
            container.style.cssText = 'position:fixed;top:0;left:0;width:100%;background:#ff0000;color:white;z-index:999999;padding:20px;font-family:monospace;font-weight:bold;text-align:left;border-bottom:2px solid white;';
            container.innerHTML += `<div>🚨 JS ERROR: ${msg}<br><span style="font-weight:normal;font-size:0.8em">${url}:${line}:${col}</span></div>`;
            document.body.prepend(container);
            return false;
        };
        window.addEventListener('unhandledrejection', function(event) {
            let container = document.getElementById('global-error-trap');
            if (!container) {
                container = document.createElement('div');
                container.id = 'global-error-trap';
                container.style.cssText = 'position:fixed;top:0;left:0;width:100%;background:#ff0000;color:white;z-index:999999;padding:15px 20px;font-family:monospace;font-size:12px;font-weight:bold;text-align:left;border-bottom:2px solid white;display:flex;justify-content:space-between;align-items:center;box-shadow:0 10px 30px rgba(0,0,0,0.5);';
                document.body.prepend(container);
            }
            
            let msg = event.reason;
            if (typeof event.reason === 'object' && event.reason !== null) {
                msg = event.reason.message || event.reason.error || JSON.stringify(event.reason);
            }
            
            container.innerHTML = `
                <div style="flex:1">⚠️ PROMISE REJECTION: ${msg}</div>
                <button onclick="this.parentElement.remove()" style="background:white;color:red;border:none;padding:4px 10px;border-radius:6px;font-size:10px;cursor:pointer;margin-left:20px;font-weight:black">CLOSE</button>
            `;
        });
    </script>

    <!-- Fonts -->

    <title>{{ config('app.name', 'AbsScroll') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            300: '#6B7AFF',
                            500: '#2D3FE6',
                            700: '#1A1F4D',
                        },
                        accent: {
                            500: '#FF2D55',
                        },
                        bg: {
                            primary: 'var(--bg-primary)',
                            secondary: 'var(--bg-secondary)',
                            tertiary: 'var(--bg-tertiary)',
                        },
                        text: {
                            primary: 'var(--text-primary)',
                            secondary: 'var(--text-secondary)',
                            tertiary: 'var(--text-tertiary)',
                        },
                        border: {
                            light: 'var(--border-light)',
                            medium: 'var(--border-medium)',
                        }
                    },
                    scale: {
                        '102': '1.02',
                        '98': '0.98',
                    }
                }
            }
        }
    </script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <script src="{{ asset('js/bridge.js') }}" defer></script>
    <script src="{{ asset('js/VideoPlayer.js') }}"></script>
    <script src="{{ asset('js/post-actions.js') }}" defer></script>
    <script src="{{ asset('js/post-render.js') }}" defer></script>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <style>
        :root {
            --primary-500: #2D3FE6;
            --primary-300: #6B7AFF;
            --primary-700: #1A1F4D;
            --accent-500: #FF2D55;
            
            /* Light Mode (Default) */
            --bg-primary: #FFFFFF;
            --bg-secondary: #F7F8FA;
            --bg-tertiary: #EBEDF0;
            --text-primary: #1C1E21;
            --text-secondary: #65676B;
            --text-tertiary: #B0B3B8;
            --border-light: #E4E6EB;
        }

        .dark {
            --bg-primary: #18191A;
            --bg-secondary: #242526;
            --bg-tertiary: #3A3B3C;
            --text-primary: #E4E6EB;
            --text-secondary: #B0B3B8;
            --text-tertiary: #8A8D91;
            --border-light: #3A3B3C;
            --border-medium: #4E4F50;
        }

        html, body {
            min-height: 100%;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
            -webkit-tap-highlight-color: transparent;
        }

        /* Strict lock ONLY when on messenger page */
        body.is-messenger {
            overflow: hidden !important;
            height: 100% !important;
            position: fixed;
            width: 100%;
            overscroll-behavior: none;
        }

        body.is-messenger #app,
        body.is-messenger #main-content {
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        body.is-messenger #main-content {
            flex: 1;
        }

        /* Default app state - allow normal scroll */
        #app {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        body.keyboard-open #bottom-navigation {
            display: none !important;
        }

        .glass {
            background: rgba(var(--bg-secondary-rgb), 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-light);
        }

        /* --- Global Premium Animations --- */
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes heart-pop {
            0% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1.2); }
            100% { opacity: 0; transform: scale(1.5); }
        }

        @keyframes presence-pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .presence-pulse { animation: presence-pulse 2s infinite ease-in-out; }
        .anim-slide-in { animation: slide-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-heart { animation: heart-pop 0.8s ease-out forwards; }
        .will-change-transform { will-change: transform, opacity; }

        /* Smooth scroll for mobile */
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--border-medium); border-radius: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }

        /* Dynamic Viewport & iOS Fixes */
        :root {
            --vh: 1vh;
        }
        


        /* Messenger specific layout classes */
        .messenger-layout {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .messenger-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        .messenger-fixed-top, .messenger-fixed-bottom {
            flex: none;
            z-index: 40;
        }

        .messenger-fixed-bottom {
            transition: margin-bottom 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 767px) {
            .messenger-fixed-bottom {
                /* Correctly lift the input above the absolute bottom-nav (64px + safe area) */
                margin-bottom: calc(70px + env(safe-area-inset-bottom));
            }

            /* When keyboard is open, global JS hides bottom-nav, so we reset the margin */
            body.keyboard-open .messenger-fixed-bottom {
                margin-bottom: 0;
            }
        }

        .h-screen {
            height: 100vh;
            height: 100dvh;
        }

        .min-h-screen {
            min-height: 100vh;
            min-height: 100dvh;
        }

        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
    <script>
        // Update dynamic vh for older browsers or specific hacks
        const updateVh = () => {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
            
            // Handle Keyboard/Visual Viewport
            if (window.visualViewport) {
                const viewport = window.visualViewport;
                const isKeyboardOpen = viewport.height < window.innerHeight;
                const app = document.getElementById('app');
                
                if (app) {
                    app.style.height = `${viewport.height}px`;
                }

                if (isKeyboardOpen) {
                    document.body.classList.add('keyboard-open');
                    // In messenger, hide global bottom nav completely when typing
                    const bottomNav = document.getElementById('bottom-navigation');
                    if (bottomNav) bottomNav.style.display = 'none';
                    
                    if (window.scrollToBottom && typeof window.scrollToBottom === 'function') {
                        setTimeout(window.scrollToBottom, 100);
                    }
                } else {
                    document.body.classList.remove('keyboard-open');
                    const bottomNav = document.getElementById('bottom-navigation');
                    if (bottomNav) bottomNav.style.display = '';
                }
            }
        };
        window.addEventListener('resize', updateVh);
        window.addEventListener('orientationchange', updateVh);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', updateVh);
        }
        updateVh();
    </script>
</head>
<body class="antialiased min-h-screen">
    <div id="app">
        <main id="main-content" class="relative">
            @yield('content')
        </main>
        
        @if(!request()->routeIs(['login', 'register', 'verify', 'password.reset', 'reels']))
            @include('partials.bottom-nav')
            @include('partials.comments-sheet')
            @include('partials.post-modals')
        @else
            {{-- For reels and other full-page routes, still include the comments sheet --}}
            @include('partials.comments-sheet')
            @include('partials.post-modals')
        @endif
    </div>

    <!-- Notification Container -->
    <div id="notifications" class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2"></div>
    
    <!-- Global Follow Helper -->
    <script src="/js/follow-helper.js"></script>

    <script>
        // Global User Context
        window.currentUser = @json(auth()->user());

        // --- BRIDGE MOCK FOR BROWSER (Global Injections) ---
        if (typeof window.bridge === 'undefined') {
            console.log('Using Global Browser Mock Bridge');
            window.bridge = {
                request: async (endpoint, options = {}) => {
                    const method = options.method || 'GET';
                    const body = options.body;
                    const headers = {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    };
                    
                    if (!(body instanceof FormData)) {
                        headers['Content-Type'] = 'application/json';
                    }

                    const url = endpoint.startsWith('http') ? endpoint : `/api/v1${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;
                    console.log(`[Mock] ${method} ${url}`);
                    
                    const res = await fetch(url, {
                        method,
                        headers,
                        body: (body && typeof body !== 'string' && !(body instanceof FormData)) ? JSON.stringify(body) : body
                    });
                    
                    let data = {};
                    try {
                        data = await res.json();
                    } catch (e) {
                        const text = await res.text().catch(() => 'No response body');
                        data = { message: `Server Error (${res.status}): ${text.substring(0, 50)}...` };
                    }
                    
                    if (!res.ok) {
                        if (res.status === 401) window.location.href = '/login';
                        throw data;
                    }
                    return data;
                },
                getToken: () => null, // Return null to allow session-based auth in browser
                me: async () => {
                    // Fetch user from internal API if needed or mock
                    return { user: { id: 1, name: 'Browser User' } }; // Simplified for now
                },
                clearToken: () => console.log('Token cleared')
            };
        }

        // Global Image Error Handler
        window.handleImageError = function(img) {
            img.onerror = null; // Prevent infinite loops
            img.src = 'https://ui-avatars.com/api/?name=Media+Unavailable&background=1a1a1a&color=333&size=512';
            img.classList.add('opacity-40', 'grayscale');
            
            // If it's a post media item, maybe add a subtle pattern
            if (img.closest('.group')) {
                img.parentElement.classList.add('empty-media-pattern');
            }
        };

        async function initUser() {
            // Global User Context - Injected immediately via Blade to prevent race conditions
            window.currentUser = @json(auth()->user());
            
            // Professional NeuralPresence Utility
            window.NeuralPresence = {
                users: new Map(),
                events: new EventEmitter(), // Simple internal event system
                
                isOnline(id) {
                    if (!id) return false;
                    return this.users.has(String(id));
                },
                
                get(id) {
                    return this.users.get(String(id));
                },
                
                statusChange(callback) {
                    this.events.on('change', callback);
                },
                state: 'connecting'
            };

            // Simple event system for internal reactivity
            function EventEmitter() {
                const listeners = {};
                this.on = (event, cb) => {
                    if (!listeners[event]) listeners[event] = [];
                    listeners[event].push(cb);
                };
                this.emit = (event, data) => {
                    if (listeners[event]) listeners[event].forEach(cb => cb(data));
                };
            }

            if (!window.currentUser) {
                console.log('[Presence] No session found, skipping presence init.');
                return;
            }

            try {
                const token = window.bridge.getToken();
                updateSidebar(window.currentUser);
                initEcho(token);
            } catch (err) {
                console.warn('[Presence] User init failed:', err);
            }
        }

        function initEcho(token) {
            // Check if window.Echo is the constructor function (from the script tag)
            if (window.Echo && typeof window.Echo === 'function') {
                const EchoClass = window.Echo;
                const authOptions = {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                };

                // Only add Bearer token if it looks like a real JWT/Sanctum token (not null/mock)
                if (token && token.length > 20) {
                    authOptions.headers['Authorization'] = `Bearer ${token}`;
                }

                window.Echo = new EchoClass({
                    broadcaster: 'pusher',
                    key: '{{ config('broadcasting.connections.pusher.key') }}',
                    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                    wsHost: window.location.hostname,
                    wsPort: {{ config('broadcasting.connections.pusher.options.port') }},
                    wssPort: {{ config('broadcasting.connections.pusher.options.port') }},
                    forceTLS: false,
                    encrypted: false,
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: '/broadcasting/auth',
                    auth: authOptions
                });

                // --- CONNECTION DIAGNOSTICS ---
                window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                    console.log(`[NeuralLink] State Change: ${states.previous} -> ${states.current}`);
                    if (window.NeuralPresence) window.NeuralPresence.state = states.current;
                    
                    if (states.current !== 'connected') {
                        window.NeuralPresence.users.clear();
                        window.NeuralPresence.events.emit('change', { type: 'reset' });
                    }
                    window.NeuralPresence.events.emit('connection-state', states.current);
                });

                window.Echo.connector.pusher.connection.bind('error', (err) => {
                    console.error('[NeuralLink] Connection Error:', err);
                });

                // Listen for global notifications
                window.Echo.private(`user.${window.currentUser.id}`)
                    .listen('.notification.received', (e) => {
                        window.toast(e.message, 'info');
                        const dot = document.getElementById('notifDot');
                        if (dot) dot.classList.remove('hidden');
                    });

                // --- NEURAL PRESENCE TRACKING ---
                window.Echo.join('app.presence')
                    .here((users) => {
                        window.NeuralPresence.users.clear();
                        users.forEach(u => {
                            const id = String(u.id);
                            window.NeuralPresence.users.set(id, u);
                        });
                        window.NeuralPresence.events.emit('change', { type: 'here', users });
                        console.log(`[NeuralPresence] Currently Online: ${users.length}`);
                    })
                    .joining((user) => {
                        const id = String(user.id);
                        window.NeuralPresence.users.set(id, user);
                        window.NeuralPresence.events.emit('change', { type: 'joining', user });
                        console.log(`[NeuralPresence] User Joined: ${user.username}`);
                    })
                    .leaving((user) => {
                        const id = String(user.id);
                        window.NeuralPresence.users.delete(id);
                        window.NeuralPresence.events.emit('change', { type: 'leaving', user });
                        console.log(`[NeuralPresence] User Left: ${user.username}`);
                    })
                    .error((err) => {
                        console.error('[NeuralPresence] Sync failed:', err);
                    });


                // Accelerated Safety Pulse: Surgical sync every 5s to catch missed Echo events
                setInterval(() => {
                    const presenceChannel = window.Echo && window.Echo.connector.pusher.channels.channels['presence-app.presence'];
                    if (presenceChannel) {
                        const members = presenceChannel.members.members;
                        const currentKeys = Array.from(window.NeuralPresence.users.keys());
                        const serverKeys = Object.keys(members).map(String);
                        
                        currentKeys.forEach(k => {
                            if (!serverKeys.includes(k)) {
                                window.NeuralPresence.users.delete(k);
                                window.NeuralPresence.events.emit('change', { type: 'leaving', user: {id: k} });
                            }
                        });
                    }
                }, 5000);

                // Aggressive Neural Shutdown: Explicitly leave and disconnect for instant offline status
                window.addEventListener('beforeunload', () => {
                    if (window.Echo) {
                        try {
                            window.Echo.leave('app.presence');
                            if (window.Echo.connector && window.Echo.connector.pusher) {
                                window.Echo.connector.pusher.disconnect();
                            }
                        } catch(e) {}
                    }
                });
            }
        }

        function updateSidebar(user) {
            const nameEl = document.getElementById('sidebarName');
            const userEl = document.getElementById('sidebarUsername');
            const avatarEl = document.getElementById('sidebarAvatar');
            const mobAvatar = document.getElementById('userAvatarMob');
            const bottomAvatar = document.getElementById('mobileAvatar');

            if (nameEl) nameEl.innerText = user.display_name;
            if (userEl) userEl.innerText = `@${user.username}`;
            if (avatarEl) avatarEl.src = user.avatar_url || `https://ui-avatars.com/api/?name=${user.username}&background=random`;
            if (mobAvatar) mobAvatar.src = user.avatar_url || `https://ui-avatars.com/api/?name=${user.username}&background=random`;
            if (bottomAvatar) bottomAvatar.src = user.avatar_url || `https://ui-avatars.com/api/?name=${user.username}&background=random`;
        }

        document.addEventListener('DOMContentLoaded', initUser);

        // Basic notification system
        window.toast = function(message, type = 'info') {
            const container = document.getElementById('notifications');
            const toast = document.createElement('div');
            const colors = {
                success: 'border-green-500',
                error: 'border-red-500',
                info: 'border-blue-500',
                warning: 'border-yellow-500'
            };
            
            toast.className = `glass p-4 rounded-lg shadow-lg border-l-4 ${colors[type] || 'border-gray-500'} animate-slide-in bg-white/10 backdrop-blur-md text-white`;
            toast.innerHTML = `<p class="text-sm font-medium">${message}</p>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('animate-slide-out');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };
        
        // Global Modal Manager
        window.toggleModal = function(id, context='show') {
            const modal = document.getElementById(id);
            if (!modal) return;
            if (context === 'show' || context === true) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        };

        // Deep-link to Create Page (Transitioned from Modal)
        document.addEventListener('DOMContentLoaded', () => {
            initUser().catch(e => console.error('initUser failed:', e));
            
            const params = new URLSearchParams(window.location.search);
            if (params.has('type')) {
                const type = params.get('type');
                window.location.href = `/create?type=${type}`;
            }
            
            // Load follow request count
            loadFollowRequestCount();
        });
        
        // Update follow request badge
        async function loadFollowRequestCount() {
            try {
                const response = await window.bridge.request('/follow-requests');
                const count = response.count || 0;
                const badge = document.getElementById('followRequestsBadge');
                
                if (badge) {
                    if (count > 0) {
                        badge.innerText = count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch (err) {
                console.log('Failed to load follow request count (silently ignored)');
            }
        }
        
        // Refresh badge count (can be called from anywhere)
        window.refreshFollowRequestsBadge = loadFollowRequestCount;

        // Global Follow State Synchronization
        // Listen for follow state changes and update ALL buttons for the same user
        window.addEventListener('followStateChanged', function(event) {
            const { userId, buttonText, buttonClasses } = event.detail;
            
            // Find and update all follow buttons for this user
            document.querySelectorAll(`button[onclick*="followUser(${userId}"`).forEach(btn => {
                // Skip the button that triggered the event (already updated)
                if (btn.innerText.trim() === buttonText.trim()) return;
                
                // Update button text and classes
                btn.innerText = buttonText;
                btn.className = buttonClasses;
            });
            
            console.log(`[FollowSync] Updated all buttons for user ${userId} to: ${buttonText}`);
        });
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
