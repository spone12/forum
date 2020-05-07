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
                    'name_notation' =>  $data_notation['name_tema'], 
                    'text_notation' => $data_notation['text_notation'],
                    'notation_add_date' =>  date("d.m.y H:i:s"),
                    'notation_edit_date' => date("d.m.y H:i:s"))
            );
        }
        else $ins = false;
       /* $ins = DB::insert('INSERT INTO notations (id_user, name_notation,text_notation) 
                            VALUES (?, ?, ?)', [$user, $data_notation['name_tema'],$data_notation['text_notation']]);*/
        //return array('dd' => 'ffds');
        return $ins;
        //return $ins;
    }

    protected function del_notation()
    {
        
    }

    protected function upd_notation()
    {
        
    }
}
