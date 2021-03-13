<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected function chat()
    {
        return view('menu.chat');
    }
}
