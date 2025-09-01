<?php

namespace App\Service\Chat\Messages;

use App\Exceptions\Chat\ChatMessageException;
use App\Contracts\Chat\Messages\MessageCommandRepositoryInterface;
use App\Service\NotificationsService;
use App\User;
use App\Models\Chat\{MessagesModel, DialogModel};
use Illuminate\Support\Facades\Gate;

/**
 * Chat message service class
 */
class MessageCommandService
{
    /** @var MessageCommandRepositoryInterface $repository */
    protected $repository;

    /**
     * ChatService constructor.
     *
     * @param MessageCommandRepositoryInterface $repository
     */
    function __construct(MessageCommandRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * Send message service
     *
     * @param array $data
     * @return array
     */
    public function send(array $data): array
    {
        $dialogId = (int)$data['dialogId'];
        $message = trim($data['message']);

        $dialog = DialogModel::findOrFail($dialogId);
        Gate::authorize('dialogAccess', $dialog);

        $messageObj = $this->repository->send($message, $dialogId);

        if (!$messageObj) {
            throw new ChatMessageException('Message was not sent!');
        }

        // Websockets
        broadcast(new \App\Events\ChatMessageEvent($messageObj));

        $participants = $dialog
            ->participants()
            ->select('user_id')
            ->where('user_id', '!=', auth()->id())
            ->get()
            ->pluck('user_id');

        foreach ($participants as $participant) {
            app(NotificationsService::class)
                ->updateUserNotificationsCache($participant, true);
        }

        return [
            'id' => $messageObj->message_id,
            'created_at' => $messageObj->created_at->toDateTimeString()
        ];
    }

    /**
     * Edit message in dialog
     *
     * @param array $data
     * @return array
     */
    public function edit(array $data):array
    {
        $messageId = (int) $data['messageId'];
        $dialogId = (int) $data['dialogId'];
        $message = trim($data['message']);

        $this->authorizeMessage($messageId);

        $messageObj = $this->repository->edit($message, $dialogId, $messageId);

        if (!$messageObj->wasChanged('text')) {
            throw new ChatMessageException('The message has not been changed!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at->toDateTimeString()
        ];
    }

    /**
     * Delete message in dialog
     *
     * @param array $data
     * @return array
     */
    public function delete(array $data):array
    {
        $messageId = (int) $data['messageId'];
        $dialogId = (int) $data['dialogId'];
        $this->authorizeMessage($messageId);

        $messageObj = $this->repository->delete($dialogId, $messageId);

        if (!$messageObj->trashed()) {
            throw new ChatMessageException('The message was not deleted!');
        }

        return [
            'id' => $messageId,
            'deleted_at' => $messageObj->deleted_at->toDateTimeString()
        ];
    }

    /**
     * Recover message in dialog
     *
     * @param array $data
     * @return
     */
    public function recover(array $data):array
    {
        $messageId = (int) $data['messageId'];
        $dialogId = (int) $data['dialogId'];
        $this->authorizeMessage($messageId, true);

        $messageObj = $this->repository->recover($dialogId, $messageId);

        if ($messageObj->trashed()) {
            throw new ChatMessageException('The message was not restored!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at->toDateTimeString()
        ];
    }

    /**
     * @param int  $messageId
     * @param bool $isTrashed
     * @return void
     */
    private function authorizeMessage(int $messageId, bool $isTrashed = false): void
    {
        $query = $isTrashed ? MessagesModel::onlyTrashed() : MessagesModel::query();
        $message = $query->findOrFail($messageId);
        Gate::authorize('messageAccess', [$message]);
    }
}
