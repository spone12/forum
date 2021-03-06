<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\NotationModel;
use App\Http\Model\Notation\NotationViewModel;
use App\Http\Requests\NotationRequest;
use App\Http\Requests\NotationPhotoRequest;
use Illuminate\Support\Facades\Auth;

class NotationController extends Controller
{
    protected function AjaxReq(NotationRequest $request)
    {
        $input = $request->only(['name_tema','text_notation']); //получение входных данных
        $method = $request->input('method');
    
        if($method == 'add')
        {
            $data = NotationModel::ins_notation($input);
        }
    
        return response()->json(['success'=> $data]);
    }

    protected function NotationView(int $notation_id)
    {
        try
        {
            NotationViewModel::add_view_notation($notation_id);
            $view = NotationModel::view_notation($notation_id);
                   
        }
        catch (\Exception $exception) 
        {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
        }

        return view('menu.notation_view', ['view' => $view]);
      
    }

    protected function NotationEditAccess(int $notation_id)
    {
        try 
        {
            $data_edit = NotationModel::data_edit_notation($notation_id);

           if($data_edit['notation']->id_user == Auth::user()->id)
                return view('menu.Notation.notation_edit', ['data_notation' => $data_edit['notation'],
                        'photo_notation' => $data_edit['notation_photos']]);
           else  return view('error_404', ['error' => ['Доступ на редактирование запрещён']]);
        } 
        catch (\Exception $exception) 
        {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
        }
    }

    protected function NotationEdit(Request $request)
    {
        if($request->ajax())
        {
            $input = $request->only(['notation_id','name_tema','text_notation']); //получение входных данных
            $edit = NotationModel::notation_edit($input);

            return response()->json(['success'=> $edit]);
        }
    }

    protected function NotationRating(Request $request)
    {
        if($request->ajax())
        {
            $input = $request->all(); //получение всех входных данных
            $back = NotationModel::notation_rating($input['notation_id'], $input['action']);

            return response()->json(['success'=> $back]);
        }
    }

    protected function NotationDelete(Request $request)
    {
        if($request->ajax())
        {
            $input = $request->only(['notation_id']); //получение всех входных данных
            $back = NotationModel::notation_delete($input['notation_id']);
           
            return response()->json(['success'=> $back]);
        }
    }

    //NotationPhotoRequest Request
    protected function NotationAddPhotos(NotationPhotoRequest $request)
    { 
        $paths = NotationModel::notation_add_photo($request);

        if(!empty($paths))
        {
            return back()
            ->with('success', "Изображения загружены успешно.")
            ->with('paths', $paths);
        }
        else 
            return back()->with('success', "Изображения загружены успешно.");
    }

    protected function NotationPhotoDelete(Request $request)
    {
        $photo_data = $request->only(['photo_id', 'notation_id']); 
        $del = NotationModel::notationPhotoDelete($photo_data);

        return response()->json(['answer'=> $del]);
    }
    
    public function Notation(Request $request)
    {
        return view('menu.notation');
    }
}
