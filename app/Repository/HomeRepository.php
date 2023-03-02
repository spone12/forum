<?php

namespace App\Repository;

use App\Enums\Profile\ProfileEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $notations = DB::table('notations AS n')
            ->select('n.notation_id', 'n.id_user',
                'n.name_notation', 'n.text_notation',
                'users.name','users.avatar','n.notation_add_date as date_n',
                'vn.counter_views', 'n.rating')
            ->join('users', 'users.id', '=', 'n.id_user')
            ->leftJoin('views_notation AS vn', 'n.notation_id', '=', 'vn.notation_id')
                ->orderBy('notation_add_date', 'DESC')
            ->paginate(5)
            ->onEachSide(2);

        if ($notations) {
            foreach ($notations as $k => $v) {

                $notations[$k]->date_n =
                    Carbon::createFromFormat('Y-m-d H:i:s', $notations[$k]->date_n)->diffForHumans();

                if (is_null($v->avatar))
                    $notations[$k]->avatar = ProfileEnum::NO_AVATAR;

                if (strlen($v->text_notation) >= 250)
                    $notations[$k]->text_notation =  Str::limit($v->text_notation, 250);
            }
        }

        return $notations;
    }
}
