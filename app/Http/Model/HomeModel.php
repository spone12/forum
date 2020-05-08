<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HomeModel extends Model
{
    protected static function take_notations()
    {
        $notations = DB::table('notations')
                    ->join('users', 'users.id', '=', 'notations.id_user')
                     ->select('notations.notation_id', 'notations.id_user',
                              'notations.name_notation', 'notations.text_notation',
                              'users.name')
                     ->orderByRaw('notation_add_date')->get();

        return $notations;
    } 
}
