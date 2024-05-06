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
     * Added exp and level to profile
     *
     * @param  $exp
     * @return int|mixed
     */
    public static function expAdd($exp)
    {
        $userData = DB::table('users AS u')
            ->select('u.id', 'dp.lvl', 'dp.exp')
            ->leftJoin('description_profile AS dp', 'dp.user_id', '=', 'u.id')
            ->where('id', '=', Auth::user()->id)
            ->first();

        $expAll = self::expGeneration($userData);
        $userData->exp += $exp;

        if ($userData->exp >= $expAll) {
            $userData->lvl++;
            $userData->exp -= $expAll;
        }

        DescriptionProfile::query()
            ->where('user_id', Auth::user()->id)
            ->update([
                'exp' => $userData->exp,
                'lvl' => $userData->lvl
            ]);
        return $exp;
    }

    /**
     * @param int $userId
     */
    public static function lvlAdd(int $userId = 0)
    {
        if (!$userId) {
            $userId = Auth::user()->id;
        }
        DescriptionProfile::where('user_id', '=', $userId)->increment('lvl');
    }

    /**
     * @param  $userData
     * @return float|int
     */
    public static function expGeneration(&$userData)
    {
        if (is_null($userData->lvl)) {
            DescriptionProfile::firstOrCreate(
                [
                    'user_id' => $userData->id
                ]
            );
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
