<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;
use Cache;

class IsUserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) 
        {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('User_is_online-' . Auth::user()->id, true, $expiresAt);
        }

        return $next($request);
    }
}
