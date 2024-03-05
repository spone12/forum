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
 * @package App\Http\Middleware
 */
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
        if (Auth::check()) {

            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('User_is_online-' . Auth::user()->id, true, $expiresAt);
            $user = User::select('last_online_at')->where('id',  Auth::user()->id)->get();

            if ($user[0]->last_online_at->diffInHours(now()) !== 0) {
                DB::table("users")
                  ->where("id", Auth::user()->id)
                  ->update(["last_online_at" => now()]);
            }
        }
        return $next($request);
    }
}
