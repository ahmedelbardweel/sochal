<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

use App\Mail\OTPVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:30', 'unique:users', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()->symbols()],
            'display_name' => ['nullable', 'string', 'max:100'],
        ]);

        // ⚠️ TEMPORARY: Skip email verification - create user immediately
        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'display_name' => $request->display_name ?? $request->username,
                'email_verified_at' => now(), // Auto-verify
                'is_verified' => true,
            ]);

            Auth::login($user); // Establish web session
            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();

            return response()->json([
                'message' => 'Account created successfully! Redirecting...',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $pendingData = Cache::get("pending_user_{$request->email}");

        if (!$pendingData || $pendingData['otp'] != $request->otp) {
            return response()->json(['message' => 'Invalid or expired verification code'], 422);
        }

        // Check again for uniqueness 
        if (User::where('email', $request->email)->exists() || User::where('username', $pendingData['username'])->exists()) {
             return response()->json(['message' => 'User already exists'], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $pendingData['username'],
                'email' => $pendingData['email'],
                'password' => $pendingData['password'],
                'display_name' => $pendingData['display_name'],
                'email_verified_at' => now(),
                'is_verified' => true,
            ]);

            Auth::login($user); // Establish web session

            Cache::forget("pending_user_{$request->email}");
            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();

            return response()->json([
                'message' => 'Neural link established. Registration complete.',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function resendRegistrationOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $pendingData = Cache::get("pending_user_{$request->email}");

        if (!$pendingData) {
            return response()->json(['message' => 'Registration session expired. Please register again.'], 422);
        }

        $otp = rand(100000, 999999);
        $pendingData['otp'] = $otp;
        
        // Refresh cache with new OTP
        Cache::put("pending_user_{$request->email}", $pendingData, now()->addMinutes(15));

        // TEMPORARILY DISABLED: SMTP not available
        /*
        try {
            Mail::to($request->email)->send(new OTPVerification($otp));
            return response()->json(['message' => 'New verification code transmitted to your terminal.']);
        } catch (\Exception $e) {
            Log::error('Resend OTP Failure: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to transmit code'], 500);
        }
        */

        return response()->json([
            'message' => 'New code generated.',
            'otp' => $otp, // ⚠️ TEMP: Remove in production
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $user = User::where($loginField, $request->email)->firstOrFail();
        
        // Security: Revoke old tokens to prevent session proliferation
        $user->tokens()->delete();

        // Update last active
        $user->update(['last_active_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Neural link active. Welcome back.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Safe token deletion (TransientToken from sessions doesn't have delete())
        if ($request->user()->currentAccessToken() && method_exists($request->user()->currentAccessToken(), 'delete')) {
            $request->user()->currentAccessToken()->delete();
        }

        // Also clear web session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->loadCount(['posts', 'followers', 'following']),
        ]);
    }
}
