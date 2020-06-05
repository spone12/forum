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

    protected static function clarification(int $gender, $avatar)
    {
        if(is_null($avatar))
        {
            $clarification['avatar'] = 'img/avatar/no_avatar.png';
        }
        return $clarification;
    }

    protected static function get_user_data_change(int $id_user)
    {
        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $data = DB::table('users')
                        ->select('users.name','users.id','description_profile.real_name', 'users.gender',
                         'description_profile.town','description_profile.date_born',
                         'description_profile.about', 'users.avatar')
                        ->leftJoin('description_profile', 'description_profile.id_user', '=', 'users.id')
                        ->where('users.id', '=', $user)
                        ->first();

            if($data)
            {
                $data_clarification = self::clarification($data->gender, $data->avatar);
                $data->avatar = $data_clarification['avatar'];
            }
           
            return $data;
        }
        else $data = false;
    }
}
