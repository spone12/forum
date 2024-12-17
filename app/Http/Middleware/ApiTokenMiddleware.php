<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\User;

class ApiTokenMiddleware
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

        $token = $request->bearerToken();
        if (!$token || !User::where('api_token', $token)->exists()) {
            return response()->json(['error' => true, 'message' => 'Unauthorized111'], 401);
        }

        $user = User::where('api_token', $token)->first();
        auth()->login($user);

        return $next($request);
    }
}
