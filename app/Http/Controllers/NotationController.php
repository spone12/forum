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
 * @package App\Http\Controllers
 */
class NotationController extends Controller
{

    /** @var NotationService */
    protected $notationService;

    /**
     * NotationController constructor
     * @param NotationService $notationService
     */
    function __construct(NotationService $notationService)
    {
        $this->notationService = $notationService;
    }

    /**
     * @param NotationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNotation(NotationRequest $request)
    {

        $input = $request->only(['notationName', 'notationText']);
        try {
            $response = $this->notationService->create($input);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json(['notationData' => $response]);
    }

    /**
     * @param int $notationId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function notationView(int $notationId)
    {

        try {
            $view = $this->notationService->view($notationId);
        } catch (\Exception $exception) {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
        }

        return view('menu.notation_view', ['view' => $view]);
    }

    /**
     * @param int $notationId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
    */
    protected function notationEditAccess(int $notationId)
    {
        try {

           $dataEdit = $this->notationService->getDataEdit($notationId);
           if ($dataEdit['notation']->user_id == Auth::user()->id) {
                return view('menu.Notation.notation_edit', [
                    'notationData' => $dataEdit['notation'],
                    'notationPhoto' => $dataEdit['notation_photo']
               ]);
           } else {
               return view('error_404', ['error' => ['Доступ на редактирование запрещён']]);
           }
        } catch (\Exception $exception) {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function notationEdit(Request $request)
    {

        if (!$request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'This is not ajax request'
            ], ResponseCodeEnum::SERVER_ERROR);
        }

        try {

            $input = $request->only(['notationId', 'notationName', 'notationText']);
            $edit = $this->notationService->update($input);
            return response()->json([
                'success' => $edit,
                'message' => 'Notation update successfully'
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function notationRating(Request $request)
    {

        if (!$request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'This is not ajax request'
            ], ResponseCodeEnum::SERVER_ERROR);
        }

        $input = $request->all();
        $isChange = $this->notationService->changeRating($input['notation_id'], $input['action']);

        return response()->json(['success'=> $isChange]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function notationDelete(Request $request)
    {

        if (!$request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'This is not ajax request'
            ], ResponseCodeEnum::SERVER_ERROR);
        }

        try {

            $input = $request->only(['notation_id']);
            $response = $this->notationService->delete($input['notation_id']);
            return response()->json([
                'success' => $response,
                'message' => 'Новость успешно удалена!'
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * @param NotationPhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    protected function notationAddPhotos(NotationPhotoRequest $request)
    {

        $photoPath = $this->notationService->addPhoto($request);
        if (!empty($photoPath)) {
            return back()->with('success', "Изображения загружены успешно.")
                ->with('paths', $photoPath);
        } else {
            return back()->with('error', "Изображения не загружены!");
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function removeNotationPhoto(Request $request)
    {

        try {
            $photoData = $request->only(['photoId', 'notationId']);
            $isDelete = $this->notationService->removePhoto($photoData);
            return response()->json([
                'success' => $isDelete,
                'message' => 'Фотография успешно удалена'
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
    */
    public function notation(Request $request)
    {
        return view('menu.notation');
    }
}
