<?php

namespace App\Repository\Chat\Messages;

use App\Contracts\Chat\Messages\MessageCommandRepositoryInterface;
use App\Models\Chat\MessagesModel;
use Illuminate\Support\Facades\{Auth, DB};

/**
 * Class ChatRepository
 *
 * @package App\Repository\Chat
 */
class MessageCommandRepository implements MessageCommandRepositoryInterface
{
    /**
     * Send message in dialog
     *
     * @param  $message  string
     * @param  $dialogId int
     *
     * @return int
     */
    public function send(string $message, int $dialogId): int
    {
        return DB::table('messages')->insertGetId([
            'dialog_id'  => $dialogId,
            'user_id'    => Auth::user()->id,
            'text'       => $message,
            'created_at' => now()
        ]);
    }

    /**
     * Edit message in dialog
     *
     * @param  $message   string
     * @param  $dialogId  int
     * @param  $messageId int
     *
     * @return MessagesModel
     */
    public function edit(string $message, int $dialogId, int $messageId): MessagesModel
    {
        $messageObj = MessagesModel::where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();

        $messageObj->text = $message;
        $messageObj->save();

        return $messageObj;
    }

    /**
     * Delete message in dialog
     *
     * @param  $dialogId  int
     * @param  $messageId int
     *
     * @return MessagesModel
     */
    public function delete(int $dialogId, int $messageId): MessagesModel
    {
        $messageObj = MessagesModel::query()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();

        $messageObj->delete();
        return $messageObj;
    }

    /**
     * Recover message in dialog
     *
     * @param  $dialogId  int
     * @param  $messageId int
     *
     * @return MessagesModel
     */
    public function recover(int $dialogId, int $messageId): MessagesModel
    {
        $messageObj = MessagesModel::onlyTrashed()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->restore();

        return $messageObj;
    }
}
