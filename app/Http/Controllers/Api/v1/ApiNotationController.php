<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notation\NotationModel;
use App\User;
use App\Enums\ResponseCodeEnum;

/**
 * Class ApiNotationController
 *
 * @package App\Http\Controllers\Api\v1
 */
class ApiNotationController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function list()
    {
        return response()->json(['notations' => auth()->user()->notations]);
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    protected function getNotationById(Request $request)
    {
        $notationObj = $this->getNotationObj($request)->get();
        return $notationObj;
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateNotation(Request $request)
    {
        $notationObj = $this->getNotationObj($request);
        $isUpdate = $notationObj->update(
            [
            'text_notation' => $request->input('text')
            ]
        );

        if ($isUpdate) {
            return response()->json(['success' => 'Notation update successfuly']);
        } else {
            return response()->json(['error' => 'Notation not updated!']);
        }
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function getNotationObj(Request $request)
    {
        $notationId = (int)$request->input('notation_id');
        $notation = NotationModel::where('notation_id', $notationId)
            ->where('user_id', auth()->user()->id);

        if (count($notation->get())) {
            return $notation;
        } else {
            return response()->json(
                [
                    'error' => 'Notation not found'
                ], ResponseCodeEnum::NOT_FOUND
            );
        }
    }
}
