<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User as User;

class ProfileModel extends Model
{
    protected $table = 'description_profile';
    protected $fillable = [
        'id_user'
    ];
    public $timestamps = false;
    public static $noAvatarPath = 'img/avatar/no_avatar.png';
    private static $typesAddExp = ['addNotation' => 10];

    protected static function getUserData() {

        if (Auth::check()) 
        {
            $data = DB::table('users AS u')
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
                ->where('id', '=', Auth::user()->id)
            ->first();
        
            if($data){
                if(is_null($data->exp))
                    $data->exp = 0;
                $data->expNeed = self::expGeneration($data);
                $data->created_at =  date_create($data->created_at)->Format('d.m.y H:i');
                
                if(!is_null($data->date_born)) {
                   $data->date_born = date_create($data->date_born)->Format('d.m.Y');
                }

                if(!is_null($data->about)){
                   $data->about = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $data->about);
                }

                $data->gender == 1 ?  $data->genderName = 'Мужской':  $data->genderName = 'Женский';
                if(is_null($data->avatar)) {
                    $data->avatar = static::$noAvatarPath;
                }
            }
        }
        else $data = false;

        return $data;
    }

    protected static function expAdd($action, $concretelyExp = 0) {
        
        $addedExp = static::$typesAddExp[$action];

        if($concretelyExp){
            $addedExp = $concretelyExp;
        }

        $userData = DB::table('users AS u')
                ->select(
                    'u.id',
                    'dp.lvl',
                    'dp.exp'
                )
                ->leftJoin('description_profile AS dp', 'dp.id_user', '=', 'u.id')
                ->where('id', '=', Auth::user()->id)
            ->first();

        $exp = self::expGeneration($userData);

        $userData->exp += $addedExp;
    
        if($userData->exp >= $exp) {
            $userData->lvl++;
            $userData->exp -= $exp;
        }

        ProfileModel::where('id_user',  Auth::user()->id)
            ->update([
                'exp' => $userData->exp, 
                'lvl' => $userData->lvl
        ]);

        return $addedExp;
    }

    protected static function lvlAdd(int $userId = 0) {

        if(!$userId)
            $userId = Auth::user()->id;

        ProfileModel::where('id_user', '=', $userId)->increment('lvl');
    }

    protected static function expGeneration(&$userData) {

        if(is_null($userData->lvl)){

            ProfileModel::firstOrCreate([
                'id_user' => $userData->id
            ]);

            $userData->lvl = 1;
        }

        return  $userData->lvl * 10;
    }

    protected static function getAnotherUser(int $id) {

        $data = DB::table('users')
            ->select('users.name','users.id', 'users.email','users.created_at',
                    'description_profile.real_name', 'users.gender',
                    'description_profile.town','description_profile.date_born',
                    'description_profile.about', 'users.avatar', 'users.last_online_at', 
                    'description_profile.phone', 'description_profile.lvl',  'description_profile.exp')
            ->leftJoin('description_profile', 'description_profile.id_user', '=', 'users.id')
            ->where('users.id', '=', $id)
        ->first();

        if(!empty($data)) {
            if(is_null($data->exp))
                $data->exp = 0;

            $data->expNeed = self::expGeneration($data, Auth::user()->id);
            $data->last_online_at =  date_create($data->last_online_at)->Format('d.m.Y H:i');
            $data->created_at =  date_create($data->created_at)->Format('d.m.Y H:i');
            $data->gender == 1 ?  $data->genderName = 'Мужской':  $data->genderName = 'Женский';
            
            if(is_null($data->avatar)) {
                $data->avatar = static::$noAvatarPath;
            }
        }

        return $data;
    }
    
    protected static function checkAvatar(&$userData) {

        if(is_null($userData->avatar)) {
            $userData->avatar = static::$noAvatarPath;
        }
    }

    protected static function getUserDataChange(int $userId = 0) {

        if (Auth::check()) 
        {
            if(!$userId)
                $userId = Auth::user()->id;

            $userData = DB::table('users')
                ->select(
                    'users.name',
                    'users.id',
                    'description_profile.real_name',
                    'users.gender',
                    'description_profile.town',
                    'description_profile.date_born',
                    'description_profile.about', 
                    'users.avatar',
                    'description_profile.phone',
                    'users.api_key'
                )
                ->leftJoin('description_profile', 'description_profile.id_user', '=', 'users.id')
                ->where('users.id', '=', $userId)
            ->first();

            if($userData) {
                self::checkAvatar($userData);
            }
           
            return $userData;
        }
        else $userData = false;
    }

    protected static function changeProfile(array $userData) {

        try
        {
            $userId = Auth::user()->id;

            if($userId === (INT)$userData['data_send']['id_user']){

                $updateProfile = 0;

                if(preg_match("/[\d]+/", $userData['data_send']['name'])) {
                    throw new \Exception('Имя не должно содержать цифры!');
                }
                
                if(Auth::user()->descriptionProfile->gender !== (INT)$userData['data_send']['gender']) {

                    $profile = DB::table('users')
                        ->where('id', '=', $userId)
                    ->update(['gender' => $userData['data_send']['gender']]);
                    $updateProfile = 1;
                }

                if (!ProfileModel::where('id_user', '=', $userId)->exists()) {

                    DB::table('description_profile')->insert(
                        array('id_user' => $userId,
                            'real_name' => $userData['data_send']['name'],
                            'date_born' =>  $userData['data_send']['date_user'],
                            'town'      => $userData['data_send']['town_user'],
                            'phone'     => $userData['data_send']['phone'],
                            'about'     => $userData['data_send']['about_user']) 
                    );
                    $updateProfile = 1;
                }
                else  {

                    DB::table('description_profile')
                    ->where('id_user', '=', $userId)
                    ->update([
                            'real_name' => $userData['data_send']['name'],
                            'date_born' =>  $userData['data_send']['date_user'],
                            'town'      => $userData['data_send']['town_user'],
                            'phone'     => $userData['data_send']['phone'],
                            'about'     => $userData['data_send']['about_user']
                    ]);
                    $updateProfile = 1;
                }

                if($updateProfile) {
                    return response()->json([
                        'status' => 1,
                        'message' => 'OK'
                    ]);
                }
            
            }
            else{
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
            $userId = Auth::user()->id;
            $imageName = uniqid() .'.'. $request->avatar->extension();  
        
            DB::table('users')
                ->where('id', $userId)
            ->update(['avatar' => "/img/avatar/user_avatar/".$userId."/".$imageName]);

            $request->avatar->move(public_path("img/avatar/user_avatar/".$userId), $imageName);
            $request->session()->put('avatar', "/img/avatar/user_avatar/". $userId ."/". $imageName);

            if(file_exists($request->session()->get('avatar'))){
                return true;
            }
        }

        return false;
    }

    public function user() {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
