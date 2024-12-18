<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notation\NotationModel;
use Illuminate\Support\Str;
use App\User;
use Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers\Api
 */
class ApiController extends Controller
{

    /**
     * Generate API Token
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function generateToken(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => true, 'message' => 'Invalid credentials'], ResponseCodeEnum::UNAUTHORIZED);
        }

        $token = $this->getRandomToken();
        $updateTokenStatus = $user->update([
            'api_token' => $token,
            'token_expires_at' => now()->addDay()
        ]);

        if (!$updateTokenStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Token not setted'
            ], ResponseCodeEnum::SERVER_ERROR);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'token_expires_at' => $user->token_expires_at->format('Y-m-d H:i:s')
        ], ResponseCodeEnum::OK);
    }

    /**
     * Get Random API token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRandomToken()
    {
        return bin2hex(random_bytes(config('app.api_token_length')));
    }
}
