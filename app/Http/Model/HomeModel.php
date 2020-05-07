<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HomeModel extends Model
{
    protected static function take_notations()
    {
        $notations = DB::table('notations')
                     ->select('notation_id', 'id_user',
                              'name_notation', 'text_notation')
                     ->orderByRaw('notation_add_date')->get();

        /*$data = [
            'user' => 'admin',
            'dd' => 23,
        ];*/

        return $notations;
    } 
}
