<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class NotationModel extends Model
{
    protected static function ins_notation(Array $data_notation)
    {
        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $ins =  
            DB::table('notations')->insert(
                array('id_user' => $user, 
                    'name_notation' =>  trim(addslashes($data_notation['name_tema'])), 
                    'text_notation' =>  trim(addslashes($data_notation['text_notation'])),
                    'notation_add_date' =>  date('Y-m-d H:i:s'),
                    'notation_edit_date' => date('Y-m-d H:i:s'))
            );
        }
        else $ins = false;

        return $ins;
        //return $ins;
    }

    protected function view_notation(int $notation_id)
    {
            $notation = DB::table('notations')
            ->join('users', 'users.id', '=', 'notations.id_user')
            ->select('notations.notation_id', 'notations.id_user',
                    'notations.name_notation', 'notations.text_notation',
                    'users.name', 'users.avatar','notations.notation_add_date')
            ->where('notations.notation_id', '=', $notation_id)->get();

            if($notation)
            {
                $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);
                
                if(is_null($notation[0]->avatar))
                    $notation[0]->avatar = 'img/avatar/no_avatar.png';

                return $notation;
            }
    
    }

    protected function edit_notation_access(int $notation_id)
    {
        $notation = DB::table('notations')
        ->select('id_user','notation_id','category','name_notation','text_notation')
        ->where('notation_id', '=', $notation_id)->first();

        return $notation;
    }

    protected function notation_rating(int $notation_id, int $action)
    {
       $check_rating = DB::table('vote_notation')
       ->select('vote_notation_id', 'vote')
       ->where('id_user', '=', Auth::user()->id)
       ->where('notation_id', '=', $notation_id)
       ->first();

       if(empty($check_rating->vote_notation_id))
       {
            $ins = DB::table('vote_notation')->insert(
            array('id_user' => Auth::user()->id, 
                  'notation_id' =>  (INT)$notation_id, 
                  'vote' => $action,
                  'vote_date' => date('Y-m-d H:i:s'))
            );

            return $ins;
       }
       else 
       {
            if($check_rating->vote == 1 && $action == 1)
                return 0;
            
            if($check_rating->vote == 0 && $action == 0)
                return 0;

            $upd = DB::table('vote_notation')
            ->where('id_user', '=', Auth::user()->id)
            ->where('notation_id', '=', $notation_id)
            ->update(['vote' => $action,
                      'vote_date' => date('Y-m-d H:i:s')]);

            return $upd;
       }
    }

    protected function del_notation()
    {
        
    }

   
}
