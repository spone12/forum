<?php

namespace App\Http\Controllers;

use App\Service\Notation\NotationService;
use Illuminate\Http\Request;
use App\Http\Requests\NotationRequest;
use App\Http\Requests\NotationPhotoRequest;
use Illuminate\Support\Facades\Auth;
use App\Enums\ResponseCodeEnum;

/**
 * Class NotationController
 *
 * @package App\Http\Controllers
 */
class NotationController extends Controller
{

    /**
     * @var NotationService
     */
    protected $notationService;

    /**
     * NotationController constructor
     *
     * @param NotationService $notationService
     */
    function __construct(NotationService $notationService)
    {
        $this->notationService = $notationService;
    }

    /**
     * Create notation
     *
     * @param  NotationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNotation(NotationRequest $request)
    {

        $input = $request->only(['notationName', 'notationText']);
        try {
            $response = $this->notationService->create($input);
        } catch (\Exception $e) {
            return response()->json(
                [
                'success' => false,
                'message' => $e->getMessage()
                ]
            );
        }
        return response()->json(['notationData' => $response]);
    }

    /**
     * Get view notation
     *
     * @param  int $notationId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function notationView(int $notationId)
    {
        try {
            $view = $this->notationService->view($notationId);
        } catch (\Exception $exception) {
            return view('error_404', ['error' => [trans('notation.errors.not_exist')]]);
        }

        return view('menu.Notation.notationView', ['view' => $view]);
    }

    /**
     * Retrieving the notation modification page
     *
     * @param  int $notationId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function notationEditView(int $notationId)
    {
        try {
            $dataEdit = $this->notationService->getDataEdit($notationId);
            if ($dataEdit['notation']->user_id !== Auth::user()->id) {
                return view(
                    'error_404', ['error' => [
                    trans('notation.errors.edit_access_denied')
                    ]]
                );
            }

            return view(
                'menu.Notation.notationEdit', [
                'notationData' => $dataEdit['notation'],
                'notationPhoto' => $dataEdit['notation_photo']
                ]
            );
        } catch (\Exception $exception) {
            return view(
                'error_404', ['error' => [
                trans('notation.errors.notation_not_found')
                ]]
            );
        }
    }

    /**
     * Update notation
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notationUpdate(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(
                [
                'success' => false,
                'message' => 'This is not ajax request'
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }

        try {
            $input = $request->only(['notationId', 'notationName', 'notationText']);
            $edit = $this->notationService->update($input);
            return response()->json(
                [
                'success' => $edit,
                'message' => trans('notation.success.update')
                ]
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                'success' => false,
                'message' => $exception->getMessage()
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }
    }

    /**
     * Change notation rating
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notationRating(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'This is not ajax request'
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }

        $input = $request->only(['notation_id', 'action']);
        $isChange = $this->notationService->changeRating($input['notation_id'], $input['action']);

        return response()->json(['success'=> $isChange]);
    }

    /**
     * Delete notation
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notationDelete(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(
                [
                'success' => false,
                'message' => 'This is not ajax request'
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }

        try {

            $input = $request->only(['notation_id']);
            $response = $this->notationService->delete($input['notation_id']);
            return response()->json(
                [
                'success' => $response,
                'message' => trans('notation.success.delete')
                ]
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                'success' => false,
                'message' => $exception->getMessage()
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }
    }

    /**
     * Add photo to notation
     *
     * @param  NotationPhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function notationAddPhoto(NotationPhotoRequest $request)
    {
        try {
            $photoPath = $this->notationService->addPhoto($request);
            return back()
                ->with('success', trans('notation.success.image_uploaded'))
                ->with('paths', $photoPath);
        } catch (\Throwable $exception) {
            return back()->with('error', trans('notation.errors.image_upload'));
        }
    }

    /**
     * Remove photo from notation
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function removeNotationPhoto(Request $request)
    {
        try {
            $photoData = $request->only(['photoId', 'notationId']);
            $isDelete = $this->notationService->removePhotoService($photoData);
            return response()->json(
                [
                'success' => $isDelete,
                'message' => trans('notation.success.image_delete')
                ]
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                'success' => false,
                'message' => $exception->getMessage()
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function notation(Request $request)
    {
        return view('menu.Notation.notation');
    }
}
