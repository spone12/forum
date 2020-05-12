<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\NotationModel;
use App\Http\Requests\NotationRequest;
use Illuminate\Support\Facades\Auth;

class NotationController extends Controller
{
    protected function AjaxReq(NotationRequest $request)
    {
        $input = $request->all(); //получение всех входных данных
        $method = $request->input('method');
    
        if($method == 'add')
        {
            $data = NotationModel::ins_notation($input);
        }
    
        return response()->json(['success'=> $data]);
    }

    public function NotationView(int $notation_id)
    {
        try
        {
            $view = NotationModel::view_notation($notation_id);
        }
        catch (\Exception $exception) 
        {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
           //abort(404,'Данной статьи не существует');
        }
        return view('menu.notation_view', ['view' => $view]);
      
    }

    protected function NotationEditAccess(int $notation_id)
    {
        try 
        {
            $edit_access = NotationModel::edit_notation_access($notation_id);

            if($edit_access->id_user == Auth::user()->id)
                return view('menu.Notation.notation_edit', ['data_notation' => $edit_access]);
            else  return view('error_404', ['error' => ['Доступ на редактирование запрещён']]);
        } 
        catch (\Exception $exception) 
        {
            return view('error_404', ['error' => ['Данной статьи не существует']]);
        }
    }

    public function Notation(Request $request)
    {
        return view('menu.notation');
    }
}
