<?php

namespace App\Service\Chat\Messages;

use App\Repository\Chat\Messages\MessageQueryRepository;
use Carbon\Carbon;
use App\Models\Chat\MessagesModel;
use App\Traits\ArrayHelper;
use App\Models\Chat\DialogModel;
use App\Service\NotificationsService;
use App\DTO\Chat\PrivateChatDTO;
use Illuminate\Support\Facades\{Gate};

/**
 * Chat service class
 */
class MessageQueryService
{
    use ArrayHelper;

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
        Gate::authorize('access', $dialog);
        $dialogMessages = $this->repository->getDialogMessages($dialogId);

        if ($dialogMessages->count()) {

            // Array of read messages
            $readMessages = [];

            foreach ($dialogMessages as $message) {
                $message->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $message->created_at)->diffForHumans();

                $this->formatChatDate($message);

                if ($message->user_id !== auth()->id()) {
                    $readMessages[] = $message->message_id;
                }
            }

            // Update read messages
            MessagesModel::whereIn('message_id', $readMessages)
                ->update(['read' => true]);
            NotificationsService::userNotifications(auth()->id(), true);
        }

        return new PrivateChatDTO(
            dialogId: $dialogId,
            messages: $dialogMessages
        );
    }

    /**
     * Formatting the text of the message composition
     *
     * @param \stdClass $obj
     *
     * @return void
     */
    private function formatChatDate(\stdClass $obj): void
    {
        $messageCreatedAt = Carbon::parse($obj->created_at);
        $obj->created_at = $messageCreatedAt->isToday()
            ? $messageCreatedAt->format('H:i')
            : $messageCreatedAt->format('d.m.Y H:i');
    }
}
