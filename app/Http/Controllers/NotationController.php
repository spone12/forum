<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\NotationModel;
use App\Http\Requests\NotationRequest;

class NotationController extends Controller
{
    public function AjaxReq(NotationRequest $request)
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
        return view('menu.notation_view_edit', ['view' => $view]);
      
    }

    public function Notation(Request $request)
    {
        //return view('menu.notation',    ['cheese' => NotationModel::ins_notation()]);
        return view('menu.notation');
    }
}
