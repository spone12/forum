<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileModel extends Model
{
    protected $table = 'description_profile';
    protected $fillable = [
        'id_user'
    ];
    public $timestamps = false;

    protected static function get_data_user()
    {
        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $data = DB::table('users  AS u')
                        ->select('u.id','u.name','u.email','u.gender', 'u.avatar',
                                'u.created_at', 'dp.real_name','dp.date_born','dp.town','dp.about')
                        ->leftJoin('description_profile AS dp', 'dp.id_user', '=', 'u.id')
                        ->where('id', '=', $user)
                        ->first();

            if($data)
            {
                if(!is_null($data->date_born))
                {
                   $data->date_born = date_create($data->date_born)->Format('d-m-Y');
                }

                if(!is_null($data->about))
                {
                   $data->about = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $data->about);
                }


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
        else
            $clarification['avatar'] = $avatar;

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

    protected static function change_profile(array $data_user)
    {
        $id_user = Auth::user()->id;
        if($id_user === (INT)$data_user['data_send']['id_user'])
        {
            $gender = DB::table('users')->select('gender')
                             ->where('id', '=', $id_user)->first();

            if($gender->gender !== (INT)$data_user['data_send']['gender'])
            {
                $upd = DB::table('users')
                ->where('id', '=', $id_user)
                ->update(['gender' => $data_user['data_send']['gender']]);
            }


            if (!ProfileModel::where('id_user', '=', $id_user)->exists())
            {
                $profile = DB::table('description_profile')->insert(
                    array('id_user' => $id_user,
                          'real_name' => $data_user['data_send']['name'],
                          'date_born' =>  $data_user['data_send']['date_user'],
                          'town' => $data_user['data_send']['town_user'],
                          'about' => $data_user['data_send']['about_user']) 
                );
            }
            else 
            {
                $profile = DB::table('description_profile')
                ->where('id_user', '=', $id_user)
                ->update(['id_user' => $id_user,
                        'real_name' => $data_user['data_send']['name'],
                        'date_born' =>  $data_user['data_send']['date_user'],
                        'town' => $data_user['data_send']['town_user'],
                        'about' => $data_user['data_send']['about_user']]);
            }

            if($profile)
            {
                return $returnData = array(
                    'status' => 1,
                    'message' => 'OK'
                );
            }
         
        }
        else
        {
            $returnData = array(
                'status' => 'error',
                'message' => 'Не совпадает ID!'
            );
            return response()->json($returnData, 500);
        }
    }

    protected static function change_avatar($request)
    {
        $id_user = Auth::user()->id;
        $imageName = uniqid().'.'.$request->avatar->extension();  
        
        DB::table('users')
            ->where('id', $id_user)
            ->update(['avatar' => "/img/avatar/user_avatar/".$id_user."/".$imageName]);

        $request->avatar->move(public_path("img/avatar/user_avatar/".$id_user), $imageName);
        $request->session()->put('avatar', "/img/avatar/user_avatar/".$id_user."/".$imageName);
    }
}
