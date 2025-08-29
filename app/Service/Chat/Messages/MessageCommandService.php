<?php

namespace App\Service\Chat\Messages;

use App\Exceptions\Chat\ChatMessageException;
use App\Contracts\Chat\Messages\MessageCommandRepositoryInterface;
use App\Service\Chat\Dialog\DialogService;
use App\User;
use App\Models\Chat\{MessagesModel, DialogModel};
use Illuminate\Database\Eloquent\Model;
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

        $this->checkDialogAccess($dialogId);
        $messageId = $this->repository->send($message, $dialogId);

        if (!$messageId) {
            throw new ChatMessageException('Message was not sent!');
        }

        // Websocket
        $message = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $user = User::select(['id', 'name', 'avatar'])
            ->whereId(auth()->id())
        ->firstOrFail();
        broadcast(new \App\Events\ChatMessageEvent($user, $message));

        return [
            'id' => $messageId,
            'created_at' => $message->created_at
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
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];
        $message = trim($data['message']);

        $this->checkDialogAccess($dialogId);
        $messageObj = $this->repository->edit($message, $dialogId, $messageId);

        if (!$messageObj->wasChanged('text')) {
            throw new ChatMessageException('The message has not been changed!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at
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
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];

        $this->checkDialogAccess($dialogId);
        $messageObj = $this->repository->delete($dialogId, $messageId);

        if (!$messageObj->trashed()) {
            throw new ChatMessageException('The message was not deleted!');
        }

        return [
            'id' => $messageId,
            'deleted_at' => $messageObj->deleted_at
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
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];

        $this->checkDialogAccess($dialogId);
        $messageObj = $this->repository->recover($dialogId, $messageId);

        if ($messageObj->trashed()) {
            throw new ChatMessageException('The message was not restored!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at
        ];
    }

    /**
     * @param int $dialogId
     * @return Model
     */
    private function checkDialogAccess(int $dialogId): Model
    {
        $dialogObject = DialogModel::findOrFail($dialogId);
        Gate::authorize('access', $dialogObject);
        return $dialogObject;
    }
}
