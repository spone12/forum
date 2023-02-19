<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\Chat\ChatService;

class ChatController extends Controller
{
    protected $chatService;

    function __construct(ChatService $chatService) {
        $this->chatService = $chatService;
    }

    /**
     * Controller user chats
     * @param Request $request
     * @return
     */
    protected function chat() {

        return view('menu.Chat.chat', ['userChats' => $this->chatService->chat()]);
    }

    /**
     * Controller search chat
     * @param Request $request
     * @return
     */
    protected function searchChat(Request $request) {

        $word = $request->only(['word']);
        $data = $this->chatService->search($word);

        return response()->json(['searched'=> $data]);
    }

    /**
     * Controller message send
     * @param Request $request
     * @return
     */
    protected function sendMessage(Request $request) {

        $data = $request->only([
            'message',
            'dialogId',
            'dialogWithId'
        ]);

        return response()->json(['message' => $this->chatService->message($data)]);
    }

    /**
     * Controller current user dialogs
     * @param $value int value - mix (dialogId or userId)
     * @param Request $request
     * @return
     */
    protected function dialog(int $value, Request $request) {

        $dialogId = $value;
        $fromProfile = $request->get('fromProfile');
        if(!is_null($fromProfile)) {

            $dialogId = $this->chatService->dialogId($value);
            $userDialog =  $this->chatService->userDialog($dialogId, $value);
        }
        else {

            $userDialog =  $this->chatService->userDialog($dialogId);
        }

        if (isset($userDialog['error'])) {
            return redirect()->route('chat')->with('error', $userDialog['error']);
        }

        return view('menu.chat.chatLS', [
            'dialogWithId' =>  $userDialog['recive'],
            'dialogObj' => $userDialog['dialogMessages'],
            'dialogId' => $dialogId,
            'lastDialogs' => $this->chatService->chat(5)
        ]);
    }
}
