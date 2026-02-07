<div id="bottom-navigation" class="md:hidden fixed !bottom-0 !top-auto left-0 right-0 z-50 bg-bg-primary/95 backdrop-blur-2xl border-t border-border-light px-6 py-3 flex items-center justify-between shadow-lg shadow-black/5 dark:shadow-[0_-8px_30px_rgba(0,0,0,0.5)] transition-transform duration-300">
    <a href="{{ route('home') }}" class="flex flex-col items-center space-y-1 {{ Request::is('home') ? 'text-primary-500' : 'text-text-tertiary' }}">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Home</span>
    </a>
    
    <a href="{{ route('explore') }}" class="flex flex-col items-center space-y-1 {{ Request::is('explore*') ? 'text-primary-500' : 'text-text-tertiary' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Explore</span>
    </a>

    <a href="{{ route('messages') }}" class="flex flex-col items-center space-y-1 {{ Request::is('messages*') ? 'text-primary-500' : 'text-text-tertiary' }}">
        <div class="relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Chats</span>
    </a>


    <a href="{{ route('notifications') }}" class="flex flex-col items-center space-y-1 {{ Request::is('notifications') ? 'text-primary-500' : 'text-text-tertiary' }}">
        <div class="relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span id="notifDotMob" class="hidden absolute top-0 right-0 w-2 h-2 bg-accent-500 rounded-full border-2 border-bg-primary"></span>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Alerts</span>
    </a>

    <a href="{{ route('profile') }}" class="flex flex-col items-center space-y-1 {{ Request::is('profile*') ? 'text-primary-500' : 'text-text-tertiary' }}">
        <img id="mobileAvatar" src="https://ui-avatars.com/api/?name=User&background=random" class="w-6 h-6 rounded-full border {{ Request::is('profile*') ? 'border-primary-500' : 'border-text-tertiary' }}">
        <span class="text-[10px] font-bold uppercase tracking-tighter">Profile</span>
    </a>
</div>
