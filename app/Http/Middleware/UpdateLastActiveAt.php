<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UpdateLastActiveAt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = 'user-last-active-' . $user->id;

            // Only update database every 1 minute for better presence precision
            if (!Cache::has($cacheKey)) {
                $user->update(['last_active_at' => now()]);
                Cache::put($cacheKey, true, Carbon::now()->addMinutes(1));
            }
        }

        return $next($request);
    }
}
