<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\Api\v1\ApiNotationModel;
use App\Http\Model\NotationModel;
use App\User;

class ApiNotationController extends Controller
{
    private $userObj;

    function __construct(Request $request) {

        $apiKey  =  request()->only('api_key');
        $apiToken = $request->bearerToken();
        $this->userObj = User::where('api_token', $apiToken)->where('api_key', $apiKey)->first();
    }

    protected function list() {
        return response()->json(['notations' => $this->userObj->notations]);
    }

    protected function getNotationById() {
        $notationId = request()->only('notation_id');
    }
}
