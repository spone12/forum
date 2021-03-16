<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\Chat\ChatModel;
class ChatController extends Controller
{
    protected function chat()
    {
        return view('menu.Chat.chat');
    }

    protected function searchChat(Request $request)
    {
        $word = $request->only(['word']);
        $data = ChatModel::searchChat(addslashes($word['word']));

        return response()->json(['success'=> $word]);
    }
}
