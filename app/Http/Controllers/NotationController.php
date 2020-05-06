<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotationModel;

class NotationController extends Controller
{
    /*public function move(Request $search)
    {
        $this->Notation($search);
    }*/

    public function Notation(Request $search)
    {
        
        return view('menu.notation',    ['cheese' => NotationModel::ins_notation()]);
    }
}
