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

    protected function sendMessage(Request $request)
    {
        $data = $request->only([
            'message', 
            'dialogId',
            'userId'
        ]);
        
        $sendMessage = ChatModel::sendMessage(addslashes($data['message']), (INT)$data['dialogId'], (INT)$data['userId']);

        return response()->json(['message' => $sendMessage]);
    }

    protected function dialog(int $userId)
    {
        //$dialog = ChatModel::dialog($userId);
        //return response()->json(['dialog'=> $dialog]);
        return view('menu.chat.chatLS', ['userId' => $userId, 'dialogId' => 0]);
    }
}
