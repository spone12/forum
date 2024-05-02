<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\Profile\ProfileEnum;

/**
 * Class HomeRepository
 *
 * @package App\Repository
 */
class HomeRepository
{

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function takeNotations()
    {

        return DB::table('notations AS n')
            ->select(
                'n.notation_id',
                'n.user_id',
                'n.name_notation',
                'n.text_notation',
                'users.name',
                'users.avatar',
                DB::raw("SUM(`nv`.`counter_views`) as counter_views"),
                'n.notation_add_date as date_n',
                'n.rating'
            )
            ->join('users', 'users.id', '=', 'n.user_id')
            ->leftJoin('notation_views AS nv', 'n.notation_id', '=', 'nv.notation_id')
            ->groupBy('nv.notation_id')
            ->orderBy('notation_add_date', 'DESC')
            ->paginate(10)
            ->onEachSide(2);
    }

    /**
     * Get auth user notifications
     *
     * @return
     */
    public function getUserNotifications()
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
                ->where('m.recive', Auth::user()->id)
            ->groupBy('m.dialog')
        ->get();
    }
}
