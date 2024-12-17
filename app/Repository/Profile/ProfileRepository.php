<?php

namespace App\Repository\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ProfileRepository
 *
 * @package App\Repository\Profile
 */
class ProfileRepository
{
    /**
     * Get user data by ID
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserData(int $userId = 0)
    {
        return DB::table('users AS u')
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'u.gender',
                'u.avatar',
                'u.api_token',
                'u.created_at',
                'u.last_online_at',
                'dp.real_name',
                'dp.date_born',
                'dp.town',
                'dp.about',
                'dp.phone',
                'dp.lvl',
                'dp.exp'
            )
            ->leftJoin('description_profile AS dp', 'dp.user_id', '=', 'u.id')
            ->where('id', '=', $userId ?: Auth::user()->id)
        ->first();
    }
}
