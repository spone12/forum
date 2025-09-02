<?php

namespace App\Repository\Chat\Notifications;

use App\Contracts\Chat\Notifications\MessageNotificationsRepositoryInterface;
use App\Enums\Profile\ProfileEnum;
use Illuminate\Support\Facades\DB;

/**
 * Class MessageNotificationsRepository
 *
 * @package App\Repository\Chat\Notifications
 */
class MessageNotificationsRepository implements MessageNotificationsRepositoryInterface
{

    /**
     * Get user notifications by user ID
     *
     * @param  int $userId
     * @return
     */
    public function getUserMessagesNotifications(int $userId)
    {
        return DB::table('messages AS m')
            ->selectRaw('COUNT(*) as count_notifications,
                         m.dialog_id,
                         users.name,
                        (CASE WHEN avatar IS NULL THEN
                            "' . ProfileEnum::NO_AVATAR . '"
                         ELSE
                            CONCAT("storage/", avatar)
                         END) as avatar'
                )
                ->join('users', 'users.id', '=', 'm.user_id')
                ->join('dialog_participants AS dp', 'dp.dialog_id', '=', 'm.dialog_id')
                ->where('dp.user_id', $userId)
                ->where('m.read', '=', 0)
                ->where('m.user_id', '!=', $userId)
                ->whereNull('m.deleted_at')
            ->groupBy('m.dialog_id')
            ->groupBy('users.name')
            ->having('count_notifications', '>', 0)
        ->get();
    }
}
