<?php

namespace App\Http\Controllers;

use App\Enums\ResponseCodeEnum;
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

        try {

            $data = $request->only([
                'message',
                'dialogId',
                'dialogWithId'
            ]);

            return response()->json(['message' => $this->chatService->message($data)]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * Edit message send
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function editMessage(Request $request)
    {

        try {

            $data = $request->only(['message', 'dialogId', 'messageId']);
            return response()->json(['edit' => $this->chatService->edit($data)]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * Delete message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleteMessage(Request $request)
    {

        try {

            $data = $request->only(['dialogId', 'messageId']);
            return response()->json(['delete' => $this->chatService->delete($data)]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], ResponseCodeEnum::SERVER_ERROR);
        }
    }

    /**
     * Controller current user dialogs
     *
     * @param int $value - mix (dialogId or userId)
     * @param int $value
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function dialog(int $value, Request $request)
    {

        try {

            $dialogId = $request->get('fromProfile') ?
                $this->chatService->dialogId($value) :
                $value;
            $userDialog = $this->chatService->userDialog($dialogId, $value);

            return view('menu.chat.chatLS', [
                'dialogWithId' =>  $userDialog['recive'],
                'dialogObj' => $userDialog['dialogMessages'],
                'dialogId' => $dialogId,
                'lastDialogs' => $this->chatService->chat(5)
            ]);
        } catch (\Throwable $exception) {
            return redirect()->route('chat')->with('errors', collect($exception->getMessage()));
        }
    }
}
