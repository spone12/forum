<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\Api\v1\ApiNotationModel;
use App\Http\Model\NotationModel;
use Auth;

class ApiNotationController extends Controller
{
    protected function list() {

        return response()->json(['notations' => NotationModel::all()]);
    }

    public function update(Request $request)
    {
        $token = Str::random(80);

        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return ['token' => $token];
    }
}
