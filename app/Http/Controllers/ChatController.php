<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\Chat\ChatService;

/**
 * Class ChatController
 * @package App\Http\Controllers
 */
class ChatController extends Controller
{
    /** @var ChatService */
    protected $chatService;

    /**
     * ChatController constructor.
     * @param ChatService $chatService
     */
    function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Controller user chats
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function chat()
    {

        return view('menu.Chat.chat', ['userChats' => $this->chatService->chat()]);
    }

    /**
     * Controller search chat
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function searchChat(Request $request)
    {

        $word = $request->only(['word']);
        $data = $this->chatService->search($word);

        return response()->json(['searched'=> $data]);
    }

    /**
     * Controller message send
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendMessage(Request $request)
    {

        $data = $request->only([
            'message',
            'dialogId',
            'dialogWithId'
        ]);

        return response()->json(['message' => $this->chatService->message($data)]);
    }

    /**
     * Controller current user dialogs
     *
     * @param int $value - mix (dialogId or userId)
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function dialog(int $value, Request $request)
    {

        $dialogId = $value;
        $fromProfile = $request->get('fromProfile');
        if (!is_null($fromProfile)) {

            $dialogId = $this->chatService->dialogId($value);
            $userDialog =  $this->chatService->userDialog($dialogId, $value);
        } else {

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
