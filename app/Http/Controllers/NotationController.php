<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notation\NotationModel;
use App\Models\Notation\NotationViewModel;
use App\Http\Requests\NotationRequest;
use App\Http\Requests\NotationPhotoRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class NotationController
 * @package App\Http\Controllers
 */
class NotationController extends Controller
{

    /**
     * @param NotationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNotation(NotationRequest $request)
    {

        $input = $request->only(['name_tema','text_notation', 'method']);
        $data = NotationModel::createNotation($input);

        return response()->json(['notationData' => $data]);
    }

    /**
     * @param int $notationId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function NotationView(int $notationId)
    {
        try
        {
            NotationViewModel::addViewNotation($notationId);
            $view = NotationModel::viewNotation($notationId);

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
    protected function NotationEditAccess(int $notationId)
    {
        try
        {

           $data_edit = NotationModel::dataEditNotation($notationId);
           if ($data_edit['notation']->user_id == Auth::user()->id) {
               return view('menu.Notation.notation_edit', ['data_notation' => $data_edit['notation'],
                   'photo_notation' => $data_edit['notation_photo']]);
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
    protected function NotationEdit(Request $request)
    {

        if ($request->ajax()) {

            $input = $request->only(['notation_id','name_tema','text_notation']);
            $edit = NotationModel::notationEdit($input);

            return response()->json(['success'=> $edit]);
        }

        return response()->json(['success'=> 'false', 'message' => 'This is not ajax request'], 500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function NotationRating(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            $back = NotationModel::notationRating($input['notation_id'], $input['action']);

            return response()->json(['success'=> $back]);
        }

        return response()->json(['success'=> 'false', 'message' => 'This is not ajax request'], 500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function NotationDelete(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->only(['notation_id']); //получение всех входных данных
            $back = NotationModel::notationDelete($input['notation_id']);

            return response()->json(['success'=> $back]);
        }

        return response()->json(['success'=> 'false', 'message' => 'This is not ajax request'], 500);
    }


    /**
     * @param NotationPhotoRequest $request
     * @return \Illuminate\Http\RedirectResponse
    */
    protected function NotationAddPhotos(NotationPhotoRequest $request)
    {
        $paths = NotationModel::notationAddPhotos($request);

        if (!empty($paths)) {

            return back()->with('success', "Изображения загружены успешно.")
                ->with('paths', $paths);
        } else {
            return back()->with('error', "Изображения не загружены!");
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    */
    protected function NotationPhotoDelete(Request $request)
    {

        $photo_data = $request->only(['photo_id', 'notation_id']);
        $del = NotationModel::notationPhotoDelete($photo_data);

        return response()->json(['answer'=> $del]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
    */
    public function Notation(Request $request)
    {
        return view('menu.notation');
    }
}
