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
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getAnotherUserData(int $id)
    {
        return DB::table('users')
            ->select(
                'users.name', 'users.id', 'users.email', 'users.created_at',
                'description_profile.real_name', 'users.gender',
                'description_profile.town', 'description_profile.date_born',
                'description_profile.about', 'users.avatar', 'users.last_online_at',
                'description_profile.phone', 'description_profile.lvl',  'description_profile.exp'
            )
            ->leftJoin('description_profile', 'description_profile.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getCurrentUserData()
    {
        return DB::table('users AS u')
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
            ->leftJoin('description_profile AS dp', 'dp.user_id', '=', 'u.id')
            ->where('id', '=', Auth::user()->id)
            ->first();
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserDataChange(int $userId = 0)
    {
        return DB::table('users')
            ->select(
                'users.name',
                'users.id',
                'users.email',
                'description_profile.real_name',
                'users.gender',
                'description_profile.town',
                'description_profile.date_born',
                'description_profile.about',
                'users.avatar',
                'description_profile.phone',
                'users.api_key'
            )
            ->leftJoin('description_profile', 'description_profile.user_id', '=', 'users.id')
            ->where('users.id', '=', $userId ?: Auth::user()->id)
            ->first();
    }
}
