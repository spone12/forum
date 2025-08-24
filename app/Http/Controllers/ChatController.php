<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\Chat\ChatService;

/**
 * Class ChatController
 *
 * @package App\Http\Controllers
 */
class ChatController extends Controller
{
    /**
     * @var ChatService
     */
    protected $chatService;

    /**
     * ChatController constructor.
     *
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
        return view('menu.Chat.chat', ['userChats' => $this->chatService->chatList()]);
    }

    /**
     * Controller current user dialogs
     *
     * @param  int     $value   - mix (dialogId or userId)
     * @param  Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function dialog(Request $request, int $value)
    {
        try {
            $dialogId = $request->get('fromProfile') ?
                app(ChatService::class)->getDialogId($value) :
                $value;
            $userDialog = $this->chatService->userDialog($dialogId, $value);

            return view('menu.Chat.chatLS', [
                'dialogWithId' => $userDialog->partnerId,
                'dialogObj'    => $userDialog->messages,
                'dialogId'     => $userDialog->dialogId,
                'lastDialogs'  => $this->chatService->chatList(5)
            ]);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('chat')
                ->with('errors', collect($exception->getMessage()));
        }
    }
}
