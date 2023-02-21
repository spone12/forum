<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\v1\ApiNotationModel;
use App\Models\NotationModel;
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

    protected function getNotationById(Request $request) {

        $notationObj = $this->getNotationObj($request)->get();
        return $notationObj;
    }

    protected function updateNotation(Request $request) {

        $notationObj = $this->getNotationObj($request);
        $isUpdate = $notationObj->update([
            'text_notation' => $request->input('text')
        ]);

        if($isUpdate){
            return response()->json([ 'success' => 'Notation update successfuly']);
        }
        else {
            return response()->json([ 'error' => 'Notation not updated!']);
        }
    }

    private function getNotationObj(Request $request) {

        $notationId = (int)$request->input('notation_id');
        $notation = NotationModel::where('notation_id', $notationId)
            ->where('id_user', $this->userObj->id);

        if(count($notation->get())) {
            return $notation;
        }
        else {
            return response()->json([ 'error' => 'Notation not found' ], 404 );
        }
    }
}
