@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-bg-primary px-4">
    <div class="max-w-md w-full glass p-10 rounded-[2.5rem] shadow-2xl space-y-8 animate-fade-in">
        <div class="text-center">
            <h2 id="viewTitle" class="text-3xl font-bold tracking-tight text-text-primary">Account Recovery</h2>
            <p id="viewDesc" class="mt-4 text-sm text-text-secondary leading-relaxed">
                Re-establish your neural credentials.
            </p>
        </div>

        <!-- Request View -->
        <form id="requestForm" class="space-y-6 {{ request('token') ? 'hidden' : '' }}">
            <div class="space-y-4">
                <div class="relative group">
                    <input type="email" id="resetEmail" placeholder="Enter your email" class="w-full h-14 bg-bg-secondary border-2 border-border-light rounded-2xl px-6 pt-2 text-sm text-text-primary focus:border-primary-500 focus:ring-0 transition-all placeholder-transparent" required>
                    <label class="absolute left-6 top-4 text-xs font-bold text-text-tertiary transition-all group-focus-within:top-2 group-focus-within:text-[10px] group-focus-within:text-primary-500 pointer-events-none">EMAIL ADDRESS</label>
                </div>
            </div>
            <button type="submit" class="w-full h-14 bg-primary-500 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:bg-primary-300 transition-all">
                SEND RECOVERY LINK
            </button>
        </form>

        <!-- Reset View -->
        <form id="resetForm" class="space-y-6 {{ !request('token') ? 'hidden' : '' }}">
            <input type="hidden" id="resetToken" value="{{ request('token') }}">
            <div class="space-y-4">
                <div class="relative group">
                    <input type="password" id="newPassword" placeholder="New Password" class="w-full h-14 bg-bg-secondary border-2 border-border-light rounded-2xl px-6 pt-2 text-sm text-text-primary focus:border-primary-500 focus:ring-0 transition-all placeholder-transparent" required>
                    <label class="absolute left-6 top-4 text-xs font-bold text-text-tertiary transition-all group-focus-within:top-2 group-focus-within:text-[10px] group-focus-within:text-primary-500 pointer-events-none">NEW PASSWORD</label>
                </div>
                <div class="relative group">
                    <input type="password" id="confirmPassword" placeholder="Confirm Password" class="w-full h-14 bg-bg-secondary border-2 border-border-light rounded-2xl px-6 pt-2 text-sm text-text-primary focus:border-primary-500 focus:ring-0 transition-all placeholder-transparent" required>
                    <label class="absolute left-6 top-4 text-xs font-bold text-text-tertiary transition-all group-focus-within:top-2 group-focus-within:text-[10px] group-focus-within:text-primary-500 pointer-events-none">CONFIRM PASSWORD</label>
                </div>
            </div>
            <button type="submit" class="w-full h-14 bg-primary-500 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:bg-primary-300 transition-all">
                UPDATE CREDENTIALS
            </button>
        </form>

        <div class="text-center pt-4">
            <a href="/login" class="text-sm font-bold text-primary-500 hover:underline tracking-tight">Return to neural link</a>
        </div>
    </div>
</div>

<script>
    // Handle Request
    document.getElementById('requestForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('resetEmail').value;
        try {
            await window.bridge.request('/auth/password/request-reset', {
                method: 'POST',
                body: JSON.stringify({ email })
            });
            window.toast('Recovery link transmitted!', 'success');
            document.getElementById('viewDesc').innerText = 'Check your email for the recovery signal.';
            e.target.classList.add('hidden');
        } catch (err) {
            window.toast(err.message, 'error');
        }
    });

    // Handle Reset
    document.getElementById('resetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const password = document.getElementById('newPassword').value;
        const password_confirmation = document.getElementById('confirmPassword').value;
        const token = document.getElementById('resetToken').value;

        if (password !== password_confirmation) return window.toast('Passwords mismatch!', 'error');

        try {
            await window.bridge.request('/auth/password/reset', {
                method: 'POST',
                body: JSON.stringify({ token, password, password_confirmation })
            });
            window.toast('Credentials updated!', 'success');
            setTimeout(() => window.location.href = '/login', 2000);
        } catch (err) {
            window.toast(err.message, 'error');
        }
    });
</script>
@endsection
