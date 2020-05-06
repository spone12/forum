<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotationController extends Controller
{
    /*public function move(Request $search)
    {
        $this->Notation($search);
    }*/

    public function Notation(Request $search)
    {
        return view('menu.notation');
    }
}
