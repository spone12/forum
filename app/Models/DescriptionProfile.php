<?php

namespace App\Models;

use App\Enums\ExpEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User as User;

/**
 * Class DescriptionProfile
 *
 * @property int $description_profile_id
 * @property int $user_id
 * @property smallint $lvl
 * @property int $exp
 * @property string|null $real_name
 * @property date|null $date_born
 * @property string|null $town
 * @property string|null $phone
 * @property string|null $about
 *
 * @package App\Models
 */
class DescriptionProfile extends Model
{
    /**
     * @var string
     */
    protected $table = 'description_profile';

    /**
     * @var string
     */
    protected $primaryKey = 'description_profile_id';

    /**
     * @var array[]
     */
    protected $fillable = [
        'user_id',
        'lvl',
        'exp',
        'real_name',
        'date_born',
        'town',
        'phone',
        'about'
    ];
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'date_born'   => 'date:d.m.Y'
    ];

    /**
     * Add exp to profile
     *
     * @param  int|float $exp
     * @param  int|null  $userId
     *
     * @return int|float
     */
    public static function expAdd($exp, ?int $userId = null)
    {
        if (is_null($userId)) {
            $userId = Auth::user()->id;
        }

        $userData = DB::table('users AS u')
            ->select('u.id', 'dp.lvl', 'dp.exp')
            ->leftJoin('description_profile AS dp', 'dp.user_id', '=', 'u.id')
            ->where('id', '=', $userId)
        ->first();

        $lvlExp = self::expGeneration($userData);
        $userData->exp += $exp;

        if ($userData->exp >= $lvlExp) {
            $userData->lvl++;
            $userData->exp -= $lvlExp;
        }

        DescriptionProfile::query()
            ->where('user_id', $userId)
            ->update([
                'exp' => $userData->exp,
                'lvl' => $userData->lvl
            ]);
        return $exp;
    }

    /**
     * Add lvl to user
     *
     * @param null|int $userId
     * @return bool
     */
    public static function lvlAdd($lvl = 1, ?int $userId = null):bool
    {
        if (is_null($userId)) {
            $userId = Auth::user()->id;
        }
        return DescriptionProfile::query()
            ->where('user_id', '=', $userId)
        ->increment('lvl', $lvl);
    }

    /**
     * Generate experience
     *
     * @param  $userData
     * @return int|float
     */
    public static function expGeneration(&$userData)
    {
        if (is_null($userData->lvl)) {
            DescriptionProfile::firstOrCreate([
                    'user_id' => $userData->id
            ]);
            $userData->lvl = 1;
        }
        return $userData->lvl * 10;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
