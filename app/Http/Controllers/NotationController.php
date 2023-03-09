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

        $input = $request->only(['name_tema','text_notation', 'method']);
        $response = $this->notationService->create($input);
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
                    'data_notation' => $dataEdit['notation'],
                    'photo_notation' => $dataEdit['notation_photo']
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

        $input = $request->only(['notation_id', 'name_tema', 'text_notation']);
        $edit = $this->notationService->edit($input);

        return response()->json(['success'=> $edit]);
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

        $input = $request->only(['notation_id']);
        $response = $this->notationService->delete($input['notation_id']);

        return response()->json(['success'=> $response]);
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

        $photoData = $request->only(['photo_id', 'notation_id']);
        $delete = $this->notationService->removePhoto($photoData);

        return response()->json(['success' => $delete]);
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
