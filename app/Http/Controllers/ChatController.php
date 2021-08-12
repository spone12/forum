<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\Chat\ChatModel;
class ChatController extends Controller
{
    protected function chat()
    {
        $userChats = ChatModel::getUserChats();
        return view('menu.Chat.chat', ['userChats' => $userChats]);
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
        $userDialog = ChatModel::getuserDialog($userId);
        
        if(isset($userDialog['error'])){
            return redirect()->route('chat');
        }

        return view('menu.chat.chatLS', ['userId' => $userId, 'dialogId' => 0]);
    }
}
