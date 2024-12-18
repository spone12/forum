<?php

namespace App;

use App\Enums\Profile\ProfileEnum;
use App\Traits\ArrayHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
 * @property smallint $gender
 * @property string $avatar
 * @property string $remember_token
 * @property date $created_at
 * @property date $updated_at
 * @property date $last_online_at
 * @property string $registration_ip
 * @property string $user_agent
 * @property string $api_token
 * @property timestamp $token_expires_at
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
        'name',
        'email',
        'password',
        'registration_ip',
        'user_agent',
        'api_token',
        'token_expires_at'
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
        'password',
        'remember_token',
        'email_verified_at',
        'registration_ip',
        'user_agent',
        'api_token',
        'token_expires_at'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_online_at' => 'datetime',
        'token_expires_at' => 'datetime'
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

    /**
     * Get user API token
     *
     * @return null|string
     */
    public function getUserAPIToken(): ?string
    {
        return $this->api_token;
    }
}
