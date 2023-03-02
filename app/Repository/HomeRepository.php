<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Class HomeRepository
 * @package App\Repository
 */
class HomeRepository
{

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function takeNotations() {

        return DB::table('notations AS n')
            ->select('n.notation_id', 'n.id_user',
                'n.name_notation', 'n.text_notation',
                'users.name','users.avatar','n.notation_add_date as date_n',
                'vn.counter_views', 'n.rating')
            ->join('users', 'users.id', '=', 'n.id_user')
            ->leftJoin('views_notation AS vn', 'n.notation_id', '=', 'vn.notation_id')
                ->orderBy('notation_add_date', 'DESC')
            ->paginate(10)
            ->onEachSide(2);
    }
}
