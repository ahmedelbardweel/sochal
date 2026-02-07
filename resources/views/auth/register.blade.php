@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex items-center justify-center bg-[#0A0E27] overflow-hidden">
    <!-- Animated background patterns -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-500 rounded-full blur-[120px] opacity-20"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-accent-500 rounded-full blur-[120px] opacity-20"></div>
    </div>

    <!-- Register Card -->
    <div class="relative z-10 w-full max-w-lg px-6 anim-fade-in-up">
        <div class="glass p-8 rounded-2xl shadow-2xl bg-white/5 border-white/10 backdrop-blur-xl">
            <!-- Logo area -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold tracking-tighter text-white mb-2">
                    Create <span class="text-primary-500">Account</span>
                </h1>
                <p class="text-text-secondary">Join the next evolution of social connectivity.</p>
            </div>

            <form id="registerForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                
                <!-- Use grid for two-column inputs on desktop -->
                <div class="space-y-2 md:col-span-2">
                    <label for="display_name" class="text-sm font-semibold text-text-secondary ml-1">Full Name</label>
                    <input type="text" id="display_name" required
                        class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300"
                        placeholder="John Doe">
                </div>

                <div class="space-y-2">
                    <label for="username" class="text-sm font-semibold text-text-secondary ml-1">Username</label>
                    <input type="text" id="username" required
                        class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300"
                        placeholder="johndoe">
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-text-secondary ml-1">Email</label>
                    <input type="email" id="email" required
                        class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300"
                        placeholder="you@example.com">
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-text-secondary ml-1">Password</label>
                    <input type="password" id="password" required
                        class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300"
                        placeholder="••••••••">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-text-secondary ml-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" required
                        class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white focus:outline-none focus:border-primary-500 transition-all duration-300"
                        placeholder="••••••••">
                </div>

                <div class="md:col-span-2 space-y-4 pt-2">
                    <p class="text-[10px] text-text-secondary text-center px-4">
                        By signing up, you agree to our <a href="#" class="text-primary-300 hover:underline">Terms of Service</a> and <a href="#" class="text-primary-300 hover:underline">Privacy Policy</a>.
                    </p>
                    
                    <button type="submit" id="registerBtn" class="w-full h-12 bg-primary-500 hover:bg-primary-300 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transform hover:scale-[1.01] active:scale-[0.99] transition-all duration-200">
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-white/10 text-center md:col-span-2">
                <p class="text-sm text-text-secondary">
                    Already part of the future? 
                    <a href="{{ route('login') }}" class="text-primary-300 font-bold hover:text-white transition-colors ml-1">Log in here</a>
                </p>
            </div>
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
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('registerBtn');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btn.innerHTML = '<span class="flex items-center justify-center space-x-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Launching...</span></span>';
            btn.disabled = true;

            const formData = {
                display_name: document.getElementById('display_name').value,
                username: document.getElementById('username').value.replace(/^@/, ''),
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            try {
                const response = await window.bridge.request('/auth/register', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
                
                // Store email AND OTP for verification step
                localStorage.setItem('pending_verification_email', formData.email);
                
                // ⚠️ TEMPORARY: Display OTP code on screen (bypassing email requirement)
                if (response.otp) {
                    localStorage.setItem('pending_otp_code', response.otp);
                }
                
                window.toast('Account created! Redirecting to verification...', 'success');
                setTimeout(() => window.location.href = '/verify', 800);
            } catch (err) {
                window.toast(err.message || 'Registration failed', 'error');
                btn.innerHTML = 'Create Account';
                btn.disabled = false;
            }
        });
    });
</script>
@endsection
