<?php

namespace App\Http\Controllers\Chat\Messages;

use App\Exceptions\Chat\ChatMessageException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Service\Chat\Messages\MessageCommandService;
use Illuminate\Http\Request;
use App\Http\Resources\{SuccessResource, ErrorResource};
use App\Http\Resources\Chat\ChatMessageResource;

/**
 * Class ChatMessageController
 *
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    /**
     * @var MessageCommandService $messageService
     */
    protected $messageService;

    /**
     * ChatMessageService constructor.
     *
     * @param MessageCommandService $messageService
     */
    function __construct(MessageCommandService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Controller: Sending a message in chat
     *
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    #[Route('/chat/send_message/', methods: ['POST'])]
    protected function send(ChatMessageRequest $request)
    {
        try {
            $data = $request->only([
                'message',
                'dialogId',
                'dialogWithId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->messageService->send($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Controller: Editing a message in a chat
     *
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    #[Route('/chat/edit_message/', methods: ['PUT'])]
    protected function edit(Request $request)
    {
        try {
            $data = $request->only([
                'message',
                'dialogId',
                'messageId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->messageService->edit($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Controller: Deleting a message in a chat
     *
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    #[Route('/chat/delete_message/', methods: ['DELETE'])]
    protected function delete(Request $request)
    {
        try {
            $data = $request->only([
                'dialogId',
                'messageId'
            ]);

            return new SuccessResource(
                new ChatMessageResource(
                    $this->messageService->delete($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }

    /**
     * Controller: Recovering a message in a chat
     *
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    #[Route('/chat/recover_message/', methods: ['PUT'])]
    protected function recover(Request $request)
    {
        try {
            $data = $request->only(['dialogId', 'messageId']);
            return new SuccessResource(
                new ChatMessageResource(
                    $this->messageService->recover($data)
                )
            );
        } catch (ChatMessageException $exception) {
            return new ErrorResource($exception->getMessage());
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }
}
