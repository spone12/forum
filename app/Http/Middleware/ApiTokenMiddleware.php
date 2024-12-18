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
        //TODO: Add API logging
        if (!$request->isJson()) {
            return response()->json(['error' => true, 'message' => 'This is not json request!'], ResponseCodeEnum::BAD_REQUEST);
        }

        $token = $request->bearerToken();
        $userObj = User::where('api_token', $token);
        if (!$token || !$userObj->exists()) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], ResponseCodeEnum::UNAUTHORIZED);
        }

        $userObj = $userObj->first();
        if (!$userObj->token_expires_at || $userObj->token_expires_at->isPast()) {
            return response()->json(['error' => true, 'message' => 'Token expired or invalid'], ResponseCodeEnum::UNAUTHORIZED);
        }

        auth()->login($userObj);
        return $next($request);
    }
}
