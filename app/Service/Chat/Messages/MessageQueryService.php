<?php

namespace App\Service\Chat\Messages;

use App\DTO\Chat\PrivateChatDTO;
use App\Models\Chat\DialogModel;
use App\Models\Chat\MessagesModel;
use App\Repository\Chat\Messages\MessageQueryRepository;
use App\Service\Chat\Notifications\MessageNotificationsService;
use Illuminate\Support\Facades\{Gate};

/**
 * Chat service class
 */
class MessageQueryService
{
    /** @var MessageQueryRepository $repository */
    protected $repository;

    /**
     * ChatService constructor.
     * @param MessageQueryRepository $repository
     */
    function __construct(MessageQueryRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Get chat dialog messages service
     *
     * @param int $dialogId
     *
     * @return PrivateChatDTO
     */
    public function getDialogMessages(int $dialogId): PrivateChatDTO
    {
        $dialog = DialogModel::findOrFail($dialogId);
        Gate::authorize('dialogAccess', $dialog);
        $dialogMessages = $this->repository->getDialogMessages($dialogId);

        if ($dialogMessages->count()) {

            // Array of read messages
            $readMessages = [];

            foreach ($dialogMessages as $message) {

                $message->user_id = $message->user->id;
                $message->avatar = $message->user->avatar;
                $message->name = $message->user->name;

                if ($message->user_id !== auth()->id()) {
                    $readMessages[] = $message->message_id;
                }
            }

            // Update read messages
            MessagesModel::whereIn('message_id', $readMessages)
                ->update(['read' => true]);

            app(MessageNotificationsService::class)
                ->updateUserNotificationsCache(isClearCache: true);
        }

        return new PrivateChatDTO(
            dialogId: $dialogId,
            messages: $dialogMessages
        );
    }
}
