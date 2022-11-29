<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\Chat\ChatModel;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected function chat() {
        $userChats = ChatModel::getUserChats();
        return view('menu.Chat.chat', ['userChats' => $userChats]);
    }

    protected function searchChat(Request $request) {
        $word = $request->only(['word']);
        $data = ChatModel::searchChat(addslashes($word['word']));

        return response()->json(['searched'=> $data]);
    }

    protected function sendMessage(Request $request) {
        $data = $request->only([
            'message',
            'dialogId',
            'dialogWithId'
        ]);

        $sendMessage = ChatModel::sendMessage(
            addslashes($data['message']), (INT) $data['dialogId'], (INT) $data['dialogWithId']
        );
        return response()->json(['message' => $sendMessage]);
    }

    /**
     * Controller Current user dialogs
     * @param $value int value - mix (dialogId or userId)
     * @param Request $request
     * @return view
     */
    protected function dialog(int $value, Request $request) {

        $dialogId = $value;
        $fromProfile = $request->get('fromProfile');
        if(!is_null($fromProfile)) {
            $dialogId = ChatModel::getDialogId($value);
            $userDialog = ChatModel::getUserDialog($dialogId, $value);
        }
        else {
            $userDialog = ChatModel::getUserDialog($dialogId);
        }

        if (isset($userDialog['error'])) {
            return redirect()->route('chat')->with('error', $userDialog['error']);
        }

        return view('menu.chat.chatLS', [
            'dialogWithId' =>  $userDialog['recive'],
            'dialogObj' => $userDialog['dialogMessages'],
            'dialogId' => $dialogId,
            'lastDialogs' => ChatModel::getUserChats(5)
        ]);
    }
}
