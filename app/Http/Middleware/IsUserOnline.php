<?php

namespace App\Http\Middleware;

use App\Enums\Cache\CacheKey;
use App\User;
use Closure;
use Carbon\Carbon;

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
        if (auth()->check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            cache()->put(CacheKey::USER_IS_ONLINE->value . auth()->id(), true, $expiresAt);

            if (auth()->user()->last_online_at->diffInMinutes(now()) > 5) {
                User::query()->whereId(auth()->id())->update(["last_online_at" => now()]);
            }
        }
        return $next($request);
    }
}
