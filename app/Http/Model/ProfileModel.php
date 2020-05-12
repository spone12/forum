<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileModel extends Model
{
    protected static function get_data_user()
    {
        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $data = DB::table('users')
                        ->select('id','name','email','gender', 'avatar',
                                'created_at')
                        ->where('id', '=', $user)
                        ->first();

            if($data)
            {
                $data->gender == 1 ?  $data->gender = 'Мужской':  $data->gender = 'Женский';
                if(is_null($data->avatar))
                {
                    $data->avatar = 'img/avatar/no_avatar.png';
                }
            }
        }
        else $data = false;

        return $data;
    }

    protected static function get_another_user($id)
    {

            $data = DB::table('users')
                        ->select('id','name','email','gender', 'avatar',
                                'created_at')
                        ->where('id', '=', $id)
                        ->first();

            if(!empty($data))
            {
                $data->gender == 1 ?  $data->gender = 'Мужской':  $data->gender = 'Женский';
                if(is_null($data->avatar))
                {
                    $data->avatar = 'img/avatar/no_avatar.png';
                }
            }
        

        return $data;
    }
}
