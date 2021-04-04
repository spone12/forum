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

        return response()->json(['searched'=> $data]);
    }

    protected function dialog(int $userId)
    {
        //$dialog = ChatModel::dialog($userId);
        //return response()->json(['dialog'=> $dialog]);
        return view('menu.chat.chatLS', ['dialog' => $userId]);
    }
}
