<nav class="hidden md:flex flex-col w-56 lg:w-64 h-screen sticky top-0 border-r border-border-light px-3 py-6 bg-bg-primary z-30">
    <div class="px-3 mb-8">
        <a href="{{ route('home') }}">
            <h1 class="text-xl font-bold tracking-tighter">
                AHM<span class="text-primary-500">ED</span>
            </h1>
        </a>
    </div>

    <div class="flex-1 space-y-2">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('home') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="{{ route('explore') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('explore') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Explore</span>
        </a>
        <a href="{{ route('notifications') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('notifications') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span id="notifDot" class="hidden absolute top-0 right-0 w-2 h-2 bg-accent-500 border-2 border-bg-primary rounded-full"></span>
            </div>
            <span>Notifications</span>
        </a>
        <a href="{{ route('messages') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('messages*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Messages</span>
        </a>
        <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('profile*') || Request::is('u/*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Profile</span>
        </a>
        <a href="{{ route('settings') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('settings') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Settings</span>
        </a>
        <a href="{{ route('create') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ Request::is('create') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/10 font-semibold' : 'hover:bg-bg-secondary text-text-secondary hover:text-text-primary' }} group transition-all w-full text-left text-sm">
            <div class="flex items-center justify-center w-5 h-5 rounded-lg border-2 border-current group-hover:border-primary-500 group-hover:text-primary-500 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="font-bold group-hover:text-primary-500 transition-colors">Create</span>
        </a>

    </div>

    <div class="mt-auto pt-4 border-t border-border-light">
        <a href="{{ route('profile') }}" class="flex items-center space-x-2 px-1 group">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-primary-500 to-accent-500 p-0.5">
                <img id="sidebarAvatar" src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->username ?? 'User').'&background=random' }}" class="w-full h-full rounded-full border border-bg-primary object-cover">
            </div>
            <div class="flex-1 overflow-hidden">
                <p id="sidebarName" class="text-sm font-bold truncate text-text-primary">{{ auth()->user()->display_name ?? 'User' }}</p>
                <p id="sidebarUsername" class="text-xs text-text-secondary truncate">@ {{ auth()->user()->username ?? 'username' }}</p>
            </div>
        </a>
    </div>
</nav>
