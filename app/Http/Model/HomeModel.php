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
                              'users.name','users.avatar')
                     ->orderByRaw('notation_add_date')->get();
        
        if($notations)
        {
            foreach($notations as $k => $v)
            {
                if(is_null($v->avatar))
                    $notations[$k]->avatar = 'img/avatar/no_avatar.png';

                //обращение к глобальной функции laravel
                if(strlen($v->text_notation) >= 250)
                    $notations[$k]->text_notation =  \Illuminate\Support\Str::limit($v->text_notation, 250);
            }
           
        }
        else $notations = 0;

        return $notations;
    } 
}
