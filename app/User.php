<?php

namespace App;

use App\Enums\Profile\ProfileEnum;
use App\Traits\ArrayHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Notation\{NotationModel, NotationCommentsModel, NotationPhotoModel, VoteNotationModel};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DescriptionProfile;
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
    use Notifiable, ArrayHelper, HasFactory;

    /**
     * The attributes that are mass assignable.
     * array fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'registration_ip', 'user_agent'
    ];

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_key', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_online_at' => "datetime"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function descriptionProfile()
    {
        return $this->hasOne(DescriptionProfile::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notations()
    {
        return $this->hasMany(NotationModel::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notationComments()
    {
        return $this->hasMany(NotationCommentsModel::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notationPhoto()
    {
        return $this->hasMany(NotationPhotoModel::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteNotation()
    {
        return $this->hasMany(VoteNotationModel::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateApiKey()
    {
        // @ TODO Rework the api key generation logic
        $countSaltCharacter = 20 - strlen(config('app.salt'));
        $apiKey = mb_substr(
            hash('sha256', config('app.salt') . Str::random($countSaltCharacter)),
            44
        );
        User::where('id', Auth::user()->id)->update(['api_key' => $apiKey]);

        return response()->json(['api_key' => $apiKey]);
    }

    /**
     * @param  int $userId
     * @return mixed
     */
    public function isOnline(int $userId)
    {
        return Cache::get('is_online.' . $userId);
    }

    /**
     * Set the default img for an empty avatar
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        return $this->attributes['avatar'] ? asset('storage/' . $this->attributes['avatar']) : asset(ProfileEnum::NO_AVATAR);
    }
}
