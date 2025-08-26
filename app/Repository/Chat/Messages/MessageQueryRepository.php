<?php

namespace App\Repository\Chat\Messages;

use App\Contracts\Chat\Messages\MessageQueryRepositoryInterface;
use App\Enums\Profile\ProfileEnum;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

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
        return DB::table('messages AS m')
            ->select(
                'm.message_id',
                'm.text',
                'm.dialog_id',
                'm.created_at',
                'm.updated_at',
                'm.user_id',
                'u.name',
                DB::raw(
                'CASE WHEN u.avatar IS NULL THEN
                        "' . ProfileEnum::NO_AVATAR . '"
                     ELSE
                        u.avatar
                     END as avatar'
                ),
                'u.id',
                'm.text'
            )
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->where('dialog_id', $dialogId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->simplePaginate($messagesPerPage);
    }
}
