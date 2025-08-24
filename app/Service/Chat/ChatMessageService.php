<?php

namespace App\Service\Chat;

use App\Exceptions\Chat\ChatMessageException;
use App\Repository\Chat\ChatMessageRepository;
use App\User;
use App\Models\Chat\MessagesModel;
use App\Traits\ArrayHelper;
use App\Models\Chat\DialogModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Chat message service class
 */
class ChatMessageService
{
    use ArrayHelper;

    /** @var ChatMessageRepository */
    protected $chatMessageRepository;

    /**
     * ChatService constructor.
     * @param ChatMessageRepository $chatRepository
     */
    function __construct(ChatMessageRepository $chatMessageRepository) {
        $this->chatMessageRepository = $chatMessageRepository;
    }

    /**
     * Send message service
     *
     * @param array $data
     * @return array
     */
    public function send(array $data): array
    {
        $dialogWithId = (int)$data['dialogWithId'];
        $dialogId = (int)$data['dialogId'];
        $message = trim($data['message']);

        $this->checkDialogAccess($dialogId);

        $messageId = $this->chatMessageRepository->sendMessage(
            $message,
            app(ChatService::class)
                ->getDialogId($dialogWithId, $dialogId)
        );

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

        $messageObj = MessagesModel::where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->text = $message;

        if (!$messageObj->save()) {
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

        $messageObj = MessagesModel::query()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->delete();

        if (!$messageObj->save()) {
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

        $messageObj = MessagesModel::onlyTrashed()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->restore();

        if (!$messageObj->save()) {
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
