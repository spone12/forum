<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Auth;
use Carbon\Carbon;
use Cache;
use DB;

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
            Cache::put('UserOnline-' . Auth::user()->id, true, $expiresAt);

            if (Auth::user()->last_online_at->diffInHours(now()) !== 0) {
                Auth::user()
                    ->update(["last_online_at" => now()]);
            }
        }
        return $next($request);
    }
}
