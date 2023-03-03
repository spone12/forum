<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Notation\NotationModel;
use App\Models\ProfileModel;
use Cache;
use Auth;

/**
 * Class User
 *
 * @property int  $id
 * @property string $name
 * @property string $email
 * @property string $email_verified_at
 * @property string $password
 * @property string $api_key
 * @property string $api_token
 * @property smallint $gender
 * @property string $avatar
 * @property string $remember_token
 * @property date $created_at
 * @property date $updated_at
 * @property date $last_online_at
 * @property date $date_change_profile
 * @property string $ip_user
 * @property string $browser_user
 *
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * array fillable fields
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'ip_user','browser_user'
    ];

    /** @var string  */
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
        'last_online_at' => "datetime"
    ];

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateApiKey() {

        $userId = Auth::user()->id;
        $countSaltCharacter = 20 - strlen(config('app.salt'));
        $apiKey = mb_substr(hash('sha256', config('app.salt') . Str::random($countSaltCharacter)), 44);
        User::where('id', $userId)->update(['api_key' => $apiKey]);

        return response()->json([ 'api_key' => $apiKey ]);
    }

    /**
     * @param int $id_user
     * @return mixed
     */
    public function isOnline(int $id_user) {
        return Cache::get('User_is_online-' . $id_user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function descriptionProfile() {
        return $this->hasOne(ProfileModel::class, 'id_user', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notations() {
        return $this->hasMany(NotationModel::class, 'id_user', 'id');
    }
}
