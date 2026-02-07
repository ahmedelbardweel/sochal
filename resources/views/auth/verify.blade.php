@extends('layouts.app')

@section('content')
<div class="relative min-h-screen flex items-center justify-center bg-[#0A0E27] overflow-hidden">
    <!-- Animated background patterns -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-500 rounded-full blur-[120px] opacity-20"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-accent-500 rounded-full blur-[120px] opacity-20"></div>
    </div>

    <!-- Verify Card -->
    <div class="relative z-10 w-full max-w-md px-4 sm:px-6 animate-fade-in">
        <div class="glass p-6 sm:p-10 rounded-3xl sm:rounded-[2.5rem] shadow-2xl bg-white/5 border-white/10 backdrop-blur-xl">
            <div class="text-center">
                <div class="w-20 h-20 bg-primary-500/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-white">Verify Neural Link</h2>
                <p class="mt-4 text-sm text-text-secondary leading-relaxed">
                    Enter the 6-digit protocol code sent to your terminal.
                </p>
            </div>

        <form id="verifyForm" class="space-y-8">
            <div class="flex justify-between gap-1 sm:gap-2">
                @for ($i = 1; $i <= 6; $i++)
                <input type="text" maxlength="1" id="digit-{{ $i }}" class="otp-input w-9 h-12 sm:w-12 sm:h-14 bg-white/5 border-2 border-white/10 rounded-xl sm:rounded-2xl text-center text-xl font-bold focus:border-primary-500 focus:ring-0 transition-all text-white">
                @endfor
            </div>

            <button type="submit" id="verifyBtn" class="w-full h-14 bg-primary-500 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:bg-primary-300 transition-all disabled:opacity-50">
                VERIFY CONNECTION
            </button>
        </form>

        <div class="text-center">
            <p class="text-sm text-text-secondary">
                Didn't receive code? 
                <button id="resendBtn" class="text-primary-500 font-bold hover:underline disabled:opacity-50">Resend (60s)</button>
            </p>
        </div>
    </div>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-input');
    
    inputs.forEach((input, index) => {
        input.addEventListener('keyup', (e) => {
            if (e.key >= 0 && e.key <= 9) {
                if (index < inputs.length - 1) inputs[index + 1].focus();
            } else if (e.key === 'Backspace') {
                if (index > 0) inputs[index - 1].focus();
            }
        });
    });

    document.getElementById('verifyForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const otp = Array.from(inputs).map(i => i.value).join('');
        if (otp.length < 6) return window.toast('Please enter full code', 'error');

        const btn = document.getElementById('verifyBtn');
        btn.disabled = true;
        btn.innerText = 'AUTHENTICATING...';

        const email = localStorage.getItem('pending_verification_email');
        
        try {
            const data = await window.bridge.request('/auth/verify-registration', {
                method: 'POST',
                body: JSON.stringify({ otp, email })
            });

            if (data.token) {
                window.bridge.setToken(data.token);
            }

            localStorage.removeItem('pending_verification_email');
            window.toast('Neural link established!', 'success');
            setTimeout(() => window.location.href = '/home', 500);
        } catch (err) {
            window.toast(err.message || 'Verification failed', 'error');
            btn.disabled = false;
            btn.innerText = 'VERIFY CONNECTION';
        }
    });

    // Handle Resend
    let cooldown = 60;
    const resendBtn = document.getElementById('resendBtn');
    
    const timer = setInterval(() => {
        if (cooldown > 0) {
            cooldown--;
            resendBtn.innerText = `Resend (${cooldown}s)`;
            resendBtn.disabled = true;
        } else {
            resendBtn.innerText = 'Resend Code';
            resendBtn.disabled = false;
            clearInterval(timer);
        }
    }, 1000);

    async function transmitCode() {
        const email = localStorage.getItem('pending_verification_email');
        if (!email) {
            window.toast('Registration session expired. Please register again.', 'error');
            return;
        }

        try {
            const data = await window.bridge.request('/auth/resend-registration-otp', { 
                method: 'POST',
                body: JSON.stringify({ email })
            });
            window.toast(data.message || 'New verification code transmitted.', 'success');
        } catch (err) {
            window.toast(err.message || 'Transmission failed', 'error');
        }
    }

    resendBtn.onclick = transmitCode;

    // Do not auto-send on load if user just registered, 
    // but show a hint that it's in their inbox.
    document.addEventListener('DOMContentLoaded', () => {
        window.toast('Check your inbox for the neural protocol code.', 'info');
    });
</script>
@endsection
