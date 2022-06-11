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
        if (!$this->checkToken($request)) {
            return response()->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        return $next($request);
    }

    private function checkToken( $request ) {

        //$token  = $request->header( 'api_token' );
        $token  = $request->only( 'api_token' ); //To do uncomment header

        return User::where( 'api_token', $token )->exists();
    }
}
