<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notation\NotationModel;
use Illuminate\Support\Str;
use App\User;
use Auth;

/**
 * Class ApiController
 * @package App\Http\Controllers\Api
 */
class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToken(Request $request)
    {

        $token = Str::random(80);
        $updateStatus = User::where('api_key', request()->only('api_key'))
            ->update(['api_token' => $token]);

        if ($updateStatus) {
            return response()->json(['api_token' => $token]);
        }

        return response()->json([
            'error' => true,
            'errorCause' => 'Api token not found!'
        ], ResponseCodeEnum::FORBIDDEN);
    }
}
