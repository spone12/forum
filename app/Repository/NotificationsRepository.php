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
                         dialog,
                         name,
                        (CASE WHEN avatar IS NULL THEN
                            "' . ProfileEnum::NO_AVATAR . '"
                         ELSE
                            avatar
                         END) as avatar'
                )
                ->join('users', 'users.id', '=', 'm.send')
                ->where('m.read', '=', 0)
                ->where('m.recive', $userId)
            ->groupBy('m.dialog')
        ->get();
    }
}
