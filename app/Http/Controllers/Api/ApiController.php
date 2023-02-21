<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotationModel;
use Illuminate\Support\Str;
use App\User;
use Auth;

class ApiController extends Controller
{
    public function updateToken(Request $request)
    {
        $token = Str::random(80);
        $updateStatus = User::where('api_key', request()->only('api_key'))->update(['api_token' => $token]);

        if($updateStatus){
           return response()->json(['api_token' => $token]);
        }
        else {

            $jsonResponse = [
                'error' => true,
                'errorCause' => 'Api token not found!'
            ];
            return response()->json([ $jsonResponse ], 403 );
        }

    }
}
