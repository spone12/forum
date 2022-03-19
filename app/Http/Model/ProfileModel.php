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
    public $noAvatarPath = 'img/avatar/no_avatar.png';

    protected static function getDataUser(){

        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $data = DB::table('users  AS u')
                        ->select(
                            'u.id',
                            'u.name',
                            'u.email',
                            'u.gender',
                            'u.avatar',
                            'u.created_at',
                            'dp.real_name',
                            'dp.date_born',
                            'dp.town',
                            'dp.about',
                            'dp.phone', 
                            'dp.lvl',
                            'dp.exp'
                        )
                        ->leftJoin('description_profile AS dp', 'dp.id_user', '=', 'u.id')
                        ->where('id', '=', $user)
                        ->first();
        
            if($data)
            {
                $data->expNeed = self::expGeneration($data->lvl);
                $data->created_at =  date_create($data->created_at)->Format('d.m.y H:i');
                
                if(!is_null($data->date_born)) {
                   $data->date_born = date_create($data->date_born)->Format('d.m.Y');
                }

                if(!is_null($data->about)){
                   $data->about = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $data->about);
                }

                $data->gender == 1 ?  $data->genderName = 'Мужской':  $data->genderName = 'Женский';
                if(is_null($data->avatar)) {
                    $data->avatar = 'img/avatar/no_avatar.png';
                }
            }
        }
        else $data = false;

        return $data;
    }

    protected static function expAdd(int $user){

    }

    protected static function lvlAdd(int $user){

    }

    protected static function expGeneration(int $lvl){
        return $lvl * 10;
    }

    protected static function getAnotherUser(int $id){

        $data = DB::table('users')
                        ->select('users.name','users.id', 'users.email','users.created_at',
                                'description_profile.real_name', 'users.gender',
                                'description_profile.town','description_profile.date_born',
                                'description_profile.about', 'users.avatar', 'users.last_online_at', 
                                'description_profile.phone')
                        ->leftJoin('description_profile', 'description_profile.id_user', '=', 'users.id')
                        ->where('users.id', '=', $id)
                        ->first();

            if(!empty($data))
            {
                $data->last_online_at =  date_create($data->last_online_at)->Format('d.m.Y H:i');
                $data->created_at =  date_create($data->created_at)->Format('d.m.Y H:i');
                $data->gender == 1 ?  $data->genderName = 'Мужской':  $data->genderName = 'Женский';
               
                if(is_null($data->avatar)) {
                    $data->avatar = 'img/avatar/no_avatar.png';
                }
            }
        

        return $data;
    }

    protected static function clarification(int $gender, $avatar) {

        if(is_null($avatar)) {
            $clarification['avatar'] = 'img/avatar/no_avatar.png';
        }
        else
            $clarification['avatar'] = $avatar;

        return $clarification;
    }

    protected static function getUserDataChange(int $id_user){

        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $data = DB::table('users')
                        ->select('users.name','users.id','description_profile.real_name', 'users.gender',
                         'description_profile.town','description_profile.date_born',
                         'description_profile.about', 'users.avatar', 'description_profile.phone')
                        ->leftJoin('description_profile', 'description_profile.id_user', '=', 'users.id')
                        ->where('users.id', '=', $user)
                        ->first();

            if($data) {
                $data_clarification = self::clarification($data->gender, $data->avatar);
                $data->avatar = $data_clarification['avatar'];
            }
           
            return $data;
        }
        else $data = false;
    }

    protected static function changeProfile(array $data_user) {

        try
        {
            $id_user = Auth::user()->id;

            if($id_user === (INT)$data_user['data_send']['id_user']){

                $updateProfile = 0;

                if(preg_match("/[\d]+/", $data_user['data_send']['name'])) {
                    throw new \Exception('Имя не должно содержать цифры!');
                }
            
                $gender = DB::table('users')->select('gender')
                                ->where('id', '=', $id_user)->first();

                            
                if($gender->gender !== (INT)$data_user['data_send']['gender']) {

                    $profile = DB::table('users')
                    ->where('id', '=', $id_user)
                    ->update(['gender' => $data_user['data_send']['gender']]);
                    $updateProfile = 1;
                
                }

                if (!ProfileModel::where('id_user', '=', $id_user)->exists())
                {
                    DB::table('description_profile')->insert(
                        array('id_user' => $id_user,
                            'real_name' => $data_user['data_send']['name'],
                            'date_born' =>  $data_user['data_send']['date_user'],
                            'town' => $data_user['data_send']['town_user'],
                            'phone' => $data_user['data_send']['phone'],
                            'about' => $data_user['data_send']['about_user']) 
                    );
                    $updateProfile = 1;
                }
                else 
                {
                    DB::table('description_profile')
                    ->where('id_user', '=', $id_user)
                    ->update(['id_user' => $id_user,
                            'real_name' => $data_user['data_send']['name'],
                            'date_born' =>  $data_user['data_send']['date_user'],
                            'town' => $data_user['data_send']['town_user'],
                            'phone' => $data_user['data_send']['phone'],
                            'about' => $data_user['data_send']['about_user']]);
                    $updateProfile = 1;
                }

                if($updateProfile)
                {
                    return response()->json([
                        'status' => 1,
                        'message' => 'OK'
                    ]);
                }
            
            }
            else
            {
                throw new \Exception('Не совпадает ID!');
            }
        }
        catch (\Exception $e) {

            return response()->json([
                'status' => 0,
                'errors'  =>  $e->getMessage(),
            ], 400);
           
        }
    }

    protected static function changeAvatar($request) {

        if($request->hasFile('avatar'))
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
}
