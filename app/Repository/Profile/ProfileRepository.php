<?php

namespace App\Repository\Profile;

use App\Enums\Profile\ProfileEnum;
use App\Enums\ResponseCodeEnum;
use App\Models\ProfileModel;
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
    public function getAnotherUser(int $id)
    {

        $data = DB::table('users')
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

        if (!empty($data)) {
            $data->expNeed = ProfileModel::expGeneration($data);
            $data->last_online_at = date_create($data->last_online_at)->Format('d.m.Y H:i');
            $data->created_at = date_create($data->created_at)->Format('d.m.Y H:i');
            $data->gender == ProfileEnum::MALE ?
                $data->genderName = trans('profile.male') :
                $data->genderName = trans('profile.female');

            if (is_null($data->avatar)) {
                $data->avatar = ProfileEnum::NO_AVATAR;
            }
        }
        return $data;
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserData()
    {

        if (Auth::check()) {
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
                ->leftJoin('description_profile AS dp', 'dp.user_id', '=', 'u.id')
                ->where('id', '=', Auth::user()->id)
                ->first();

            if ($data) {
                $data->expNeed = ProfileModel::expGeneration($data);
                $data->created_at = date_create($data->created_at)->Format('d.m.y H:i');

                if (!is_null($data->date_born)) {
                    $data->date_born = date_create($data->date_born)->Format('d.m.Y');
                }

                if (!is_null($data->about)) {
                    $data->about = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $data->about);
                }

                $data->gender == ProfileEnum::MALE ?
                    $data->genderName = trans('profile.male') :
                    $data->genderName = trans('profile.female');

                if (is_null($data->avatar)) {
                    $data->avatar = ProfileEnum::NO_AVATAR;
                }
            }
        }
        else { $data = false;
        }

        return $data;
    }


    /**
     * @param  int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserDataChange(int $userId = 0)
    {

        $userData = DB::table('users')
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

        if ($userData) {
            self::checkAvatar($userData);
        }
        return $userData;
    }

    /**
     * @param  array $userData
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProfile(array $userData)
    {

        try {

            $userId = Auth::user()->id;
            if ($userId === (INT)$userData['data_send']['user_id']) {
                $updateProfile = 0;
                if (preg_match("/[\d]+/", $userData['data_send']['name'])) {
                    throw new \Exception('Имя не должно содержать цифры!');
                }

                if (Auth::user()->descriptionProfile->gender !== (INT)$userData['data_send']['gender']) {
                    DB::table('users')
                        ->where('id', '=', $userId)
                        ->update(['gender' => $userData['data_send']['gender']]);
                    $updateProfile = 1;
                }

                // @todo Refactor to insert or update
                if (!ProfileModel::where('user_id', '=', $userId)->exists()) {
                    DB::table('description_profile')->insert(
                        [
                        'user_id'   => $userId,
                        'real_name' => $userData['data_send']['name'],
                        'date_born' => $userData['data_send']['date_user'],
                        'town'      => $userData['data_send']['town_user'],
                        'phone'     => $userData['data_send']['phone'],
                        'about'     => $userData['data_send']['about_user']
                        ]
                    );

                    $updateProfile = 1;
                } else {
                    DB::table('description_profile')
                        ->where('user_id', '=', $userId)
                        ->update(
                            [
                            'real_name' => $userData['data_send']['name'],
                            'date_born' => $userData['data_send']['date_user'],
                            'town'      => $userData['data_send']['town_user'],
                            'phone'     => $userData['data_send']['phone'],
                            'about'     => $userData['data_send']['about_user']
                            ]
                        );

                    $updateProfile = 1;
                }

                if ($updateProfile) {
                    return response()->json(
                        [
                        'status' => 1,
                        'message' => 'OK'
                        ]
                    );
                }
            } else {
                throw new \Exception('Не совпадает ID!');
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                'status' => 0,
                'errors'  =>  $e->getMessage(),
                ], ResponseCodeEnum::BAD_REQUEST
            );
        }
    }

    /**
     * @param  $request
     * @return bool
     */
    public function changeAvatar($request)
    {

        if ($request->hasFile('avatar')) {
            $userId = Auth::user()->id;
            $imageName = uniqid() . '.' . $request->avatar->extension();

            DB::table('users')
                ->where('id', $userId)
                ->update(['avatar' => ProfileEnum::USER_AVATAR_PATH . $userId . '/' . $imageName]);

            $request->avatar->move(public_path(ProfileEnum::USER_AVATAR_PATH . $userId), $imageName);
            $request->session()->put('avatar', ProfileEnum::USER_AVATAR_PATH . $userId . '/' . $imageName);

            if (file_exists($request->session()->get('avatar'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $userData
     */
    protected static function checkAvatar(&$userData)
    {

        if (is_null($userData->avatar)) {
            $userData->avatar = ProfileEnum::NO_AVATAR;
        }
    }
}
