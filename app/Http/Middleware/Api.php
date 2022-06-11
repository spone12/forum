<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Auth;

class Api
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
        if (!$this->checkAuthorization($request)) {
            return response()->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        return $next($request);
    }

    private function checkAuthorization( $request ) {

        $token   = $request->header('Authorization');
        $apiKey  =  request()->only('api_key');
        $authToken = trim(str_ireplace('Bearer', '', $token));

        return User::where('api_token', $authToken)->where('api_key', $apiKey)->exists();
    }
}
