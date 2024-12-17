<?php

namespace App\Http\Middleware;

use App\Enums\ResponseCodeEnum;
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

        if (!$request->isJson()) {
            return response()->json(['error' => true, 'message' => 'This is not json request!'], ResponseCodeEnum::BAD_REQUEST);
        }

        $token = $request->bearerToken();
        if (!$token || !User::where('api_token', $token)->exists()) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], ResponseCodeEnum::UNAUTHORIZED);
        }

        $user = User::where('api_token', $token)->first();
        auth()->login($user);

        return $next($request);
    }
}
