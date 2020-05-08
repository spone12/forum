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
                    'notation_add_date' =>  date("d.m.y H:i:s"),
                    'notation_edit_date' => date("d.m.y H:i:s"))
            );
        }
        else $ins = false;

        return $ins;
        //return $ins;
    }

    protected function view_notation(int $notation_id)
    {
        if (Auth::check()) 
        {

            $notation = DB::table('notations')
            ->join('users', 'users.id', '=', 'notations.id_user')
            ->select('notations.notation_id', 'notations.id_user',
                    'notations.name_notation', 'notations.text_notation',
                    'users.name','notations.notation_add_date')
            ->where('notations.notation_id', '=', $notation_id)->get();

            if($notation)
            {
                $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);
               
                return $notation;
            }
            else return 0;    
        }
    }

    protected function del_notation()
    {
        
    }

    protected function upd_notation()
    {
        
    }
}
