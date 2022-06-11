<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Cache;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * массив данных при создании пользователя user_create
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'ip_user','browser_user'
    ];
    
    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        "last_online_at" => "datetime"
    ];

    public function generateApiKey() {

        $userId = Auth::user()->id;
        $countSaltCharacter = 20 - strlen(config('app.salt'));
        $apiKey = mb_substr(hash('sha256', config('app.salt') . Str::random($countSaltCharacter)), 44);
        User::where('id', $userId)->update(['api_key' => $apiKey]);

        return response()->json([ 'api_key' => $apiKey ]);
    }

    public function isOnline(int $id_user)
    {
         return Cache::get('User_is_online-' . $id_user);
    }
}
