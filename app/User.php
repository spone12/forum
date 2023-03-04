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
 * @property string $registration_ip
 * @property string $user_agent
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
        'name', 'email', 'password', 'registration_ip', 'user_agent'
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
     * @param int $userId
     * @return mixed
     */
    public function isOnline(int $userId) {
        return Cache::get('User_is_online-' . $userId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function descriptionProfile() {
        return $this->hasOne(ProfileModel::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notations() {
        return $this->hasMany(NotationModel::class, 'user_id', 'id');
    }
}
