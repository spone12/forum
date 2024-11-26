<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

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
    public function takeNotations(string $order = '')
    {
        $notations = DB::table('notations AS n')
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
            ->groupBy('nv.notation_id');

        if ($order === '') {
            $notations->orderBy('notation_add_date', 'DESC');
        } else {
            $notations->orderBy($order, 'DESC');
        }

        return $notations
                ->paginate(10)
                ->onEachSide(2);
    }
}
