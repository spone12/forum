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
        if(request()->only('update_token') && request()->only('api_key') && $request->isMethod('put')) {
            return $next($request);
        }


        if (!$this->checkAuthorization($request)) {
            return response()->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        return $next($request);
    }

    private function checkAuthorization( $request ) {

        $token   = $request->header('Authorization');

        if(!preg_match("/Bearer/i", $token))  {
            return false;
        }

        $apiKey  =  request()->only('api_key');
        $authToken = trim(str_ireplace('Bearer', '', $token));

        return User::whereNotNull('api_token')->where('api_token', $authToken)->where('api_key', $apiKey)->exists();
    }
}
