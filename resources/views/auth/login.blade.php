@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex items-center justify-center bg-[#0A0E27] overflow-hidden">
    <!-- Animated background patterns -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-500 rounded-full blur-[120px] opacity-20"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-accent-500 rounded-full blur-[120px] opacity-20"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md px-6 anim-fade-in-up">
        <div class="glass p-8 rounded-2xl shadow-2xl bg-white/5 border-white/10 backdrop-blur-xl">
            <!-- Logo area -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold tracking-tighter text-white mb-2">
                    AHM<span class="text-primary-500">ED</span>
                </h1>
                <p class="text-text-secondary">Welcome back to the future of social.</p>
            </div>

            <form id="loginForm" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-text-secondary ml-1">Email or Username</label>
                    <div class="relative group">
                        <input type="text" name="email" id="email" required
                            class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300 group-hover:border-white/20"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label for="password" class="text-sm font-semibold text-text-secondary">Password</label>
                        <a href="{{ route('password.reset') }}" class="text-xs text-primary-300 hover:text-white transition-colors">Forgot Password?</a>
                    </div>
                    <div class="relative group">
                        <input type="password" name="password" id="password" required
                            class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300 group-hover:border-white/20"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center space-x-2 ml-1">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <label for="remember" class="text-sm text-text-secondary">Remember me</label>
                </div>

                <button type="submit" id="loginBtn" class="w-full h-12 bg-primary-500 hover:bg-primary-300 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                    Sign In
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-sm text-text-secondary">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-primary-300 font-bold hover:text-white transition-colors ml-1">Join the community</a>
                </p>
            </div>
        </div>
        
        <!-- App download/links links -->
        <div class="mt-8 flex justify-center gap-6 text-white/40 text-xs">
            <a href="#" class="hover:text-white transition-colors">Privacy</a>
            <a href="#" class="hover:text-white transition-colors">Terms</a>
            <a href="#" class="hover:text-white transition-colors">Support</a>
        </div>
    </div>
</div>

<style>
    .glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .anim-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btn.innerHTML = '<span class="flex items-center justify-center space-x-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Entering...</span></span>';
            btn.disabled = true;

            try {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                const response = await window.bridge.login(email, password);
                
                window.toast('Welcome back!', 'success');
                
                setTimeout(() => {
                    if (response.user.role === 'admin') {
                        window.location.href = '/admin/dashboard';
                    } else {
                        window.location.href = '/home';
                    }
                }, 800);
            } catch (err) {
                window.toast(err.message || 'Login failed', 'error');
                btn.innerHTML = 'Sign In';
                btn.disabled = false;
            }
        });
    });
</script>
@endsection
