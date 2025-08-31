<?php

namespace App\Repository\Chat\Messages;

use App\Contracts\Chat\Messages\MessageQueryRepositoryInterface;
use App\Models\Chat\MessagesModel;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * Class MessageQueryRepository
 *
 * @package App\Repository\Chat\Messages
 */
class MessageQueryRepository implements MessageQueryRepositoryInterface
{
    /**
     * Get dialog messages by id
     *
     * @param int $dialogId
     * @param int $messagesPerPage
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getDialogMessages(int $dialogId, int $messagesPerPage = 10): Paginator
    {
        return MessagesModel::query()
            ->select(
                'message_id',
                'text',
                'dialog_id',
                'created_at',
                'updated_at',
                'user_id',
                'text'
            )
            ->where('dialog_id', $dialogId)
            ->orderByDesc('created_at')
            ->simplePaginate($messagesPerPage);
    }
}
