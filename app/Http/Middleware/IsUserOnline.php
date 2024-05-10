<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Auth;
use Carbon\Carbon;
use Cache;

/**
 * Class IsUserOnline
 *
 * @package App\Http\Middleware
 */
class IsUserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('is_online.' . Auth::user()->id, true, $expiresAt);

            if (Auth::user()->last_online_at->diffInMinutes(now()) > 5) {
                User::query()->whereId(Auth::user()->id)->update(["last_online_at" => now()]);
            }
        }
        return $next($request);
    }
}
