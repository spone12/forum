<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use App\Enums\Profile\ProfileEnum;

/**
 * Class NotificationsRepository
 *
 * @package App\Repository
 */
class NotificationsRepository
{

    /**
     * Get user notifications by Id
     *
     * @param  int $userId
     * @return
     */
    public static function getUserNotifications(int $userId)
    {
        return DB::table('messages AS m')
            ->selectRaw('COUNT(*) as count_notifications,
                         m.dialog_id,
                         name,
                        (CASE WHEN avatar IS NULL THEN
                            "' . ProfileEnum::NO_AVATAR . '"
                         ELSE
                            avatar
                         END) as avatar'
                )
                ->join('users', 'users.id', '=', 'm.user_id')
                ->join('dialog_participants AS dp', 'dp.dialog_id', '=', 'm.dialog_id')
                ->where('dp.user_id', $userId)
                ->where('m.read', '=', 0)
                ->where('m.user_id', '!=', $userId)
            ->groupBy('m.dialog_id')
            ->having('count_notifications', '>', 0)
        ->get();
    }
}
