<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use App\Mail\OTPVerification;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

class SecurityController extends Controller
{
    /**
     * Send OTP for Email Verification
     */
    public function sendVerification(Request $request)
    {
        $user = $request->user();
        $otp = rand(100000, 999999);
        
        // Storage
        $user->update(['metadata->otp' => $otp]);

        try {
            // Actual Email sending
            Mail::to($user->email)->send(new OTPVerification($otp));

            return response()->json([
                'message' => 'Verification code transmitted to your email',
            ]);
        } catch (\Exception $e) {
            Log::error('SMTP Verification Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to transmit code. Please check your neural link configuration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        $user = $request->user();

        if ($request->otp == ($user->metadata['otp'] ?? null)) {
            $user->update([
                'email_verified_at' => now(),
                'metadata->otp' => null
            ]);
            return response()->json(['message' => 'Neural link verified successfully']);
        }

        return response()->json(['message' => 'Invalid or expired verification code'], 422);
    }



    /**
     * Request Password Reset
     */
    public function requestReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(64);
            $user->update(['metadata->reset_token' => $token]);
            
            try {
                // Actual Email sending
                Mail::to($user->email)->send(new PasswordResetMail($token));
            } catch (\Exception $e) {
                Log::error('SMTP Password Reset Error: ' . $e->getMessage());
                // Silently fail to prevent enumeration via timing/error
            }
        }

        // Generic response for enumeration protection
        return response()->json([
            'message' => 'Recovery protocol initiated. If this email is registered, you will receive a reset link shortly.'
        ]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $user = User::where('metadata->reset_token', $request->token)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
            'metadata->reset_token' => null
        ]);

        return response()->json(['message' => 'Password reset successful. You may now log in.']);
    }
}
