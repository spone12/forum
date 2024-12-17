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
     * Get user notations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function list()
    {
        return response()->json(['notations' => auth()->user()->notations]);
    }

    /**
     * Get notation by ID
     *
     * @param  Request $request
     * @return mixed
     */
    protected function getNotationById(Request $request)
    {

        $notationId = (int)$request->input('notation_id');
        $notationObj = NotationModel::where('notation_id', $notationId)
            ->where('user_id', auth()->user()->id);

        if ($notationObj->exists()) {
            return response()->json([$notationObj->first()]);
        } else {
            return response()->json(
                [
                    'error' => 'Notation not found'
                ], ResponseCodeEnum::NOT_FOUND
            );
        }
    }

    /**
     * Update notation
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateNotation(Request $request)
    {

        if (!$request->has('notation_id')) {
            return response()->json(['error' => 'parameter “notation_id” has not been passed'], ResponseCodeEnum::BAD_REQUEST);
        }

        if (!$request->has('text')) {
            return response()->json(['error' => 'parameter "text" has not been passed'], ResponseCodeEnum::BAD_REQUEST);
        }

        $notationId = (int)$request->input('notation_id');
        $notationObj = NotationModel::where('notation_id', $notationId)
            ->where('user_id', auth()->user()->id);

        if ($notationObj->exists()) {
            $notationObj = $notationObj->first();
        } else {
            return response()->json(
                [
                    'error' => 'Notation not found'
                ], ResponseCodeEnum::NOT_FOUND
            );
        }

        $isUpdate = $notationObj->update([
            'text_notation' => htmlspecialchars(trim($request->input('text')))
        ]);

        if ($isUpdate) {
            return response()->json(['success' => 'Notation update successfuly'], ResponseCodeEnum::OK);
        } else {
            return response()->json(['error' => 'Notation not updated!'], ResponseCodeEnum::SERVER_ERROR);
        }
    }
}
