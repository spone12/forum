<?php

namespace App\Http\Controllers;

use App\Enums\ResponseCodeEnum;
use App\Exceptions\Chat\ChatMessageException;
use App\Http\Requests\ChatMessageRequest;
use Illuminate\Http\Request;
use App\Service\Chat\ChatService;
use App\Http\Resources\{SuccessResource, ErrorResource};
use App\Http\Resources\Chat\ChatMessageResource;

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
        return view('menu.Chat.chat', ['userChats' => $this->chatService->chat()]);
    }

    /**
     * Controller search chat
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function searchChat(Request $request)
    {
        $word = $request->only('word');
        $data = $this->chatService->search($word);
        return response()->json(['searchResult' => $data]);
    }

    /**
     * Controller message send
     *
     * @param  Request $request
     * @return ErrorResource|SuccessResource
     */
    protected function sendMessage(ChatMessageRequest $request)
    {
        try {
            $data = $request->only([
                'message',
                'dialogId',
                'dialogWithId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->chatService->send($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Chat edit message controller
     *
     * @param  Request $request
     * @return ErrorResource|SuccessResource
     */
    protected function editMessage(Request $request)
    {
        try {
            $data = $request->only([
                'message',
                'dialogId',
                'messageId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->chatService->edit($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Chat delete message controller
     *
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    protected function deleteMessage(Request $request)
    {
        try {
            $data = $request->only([
                'dialogId',
                'messageId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->chatService->delete($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Chat recover message controller
     *
     * @param  Request $request
     * @return SuccessResource|ErrorResource
     */
    protected function recoverMessage(Request $request)
    {
        try {
            $data = $request->only(['dialogId', 'messageId']);
            return new SuccessResource(
                new ChatMessageResource(
                    $this->chatService->recover($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
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
                $this->chatService->getDialogId($value) :
                $value;
            $userDialog = $this->chatService->userDialog($dialogId, $value);

            return view('menu.Chat.chatLS', [
                'dialogWithId' => $userDialog->partnerId,
                'dialogObj'    => $userDialog->messages,
                'dialogId'     => $userDialog->dialogId,
                'lastDialogs'  => $this->chatService->chat(5)
            ]);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('chat')
                ->with('errors', collect($exception->getMessage()));
        }
    }
}
