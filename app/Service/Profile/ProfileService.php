<?php

namespace App\Service\Profile;

use App\Enums\NotationEnum;
use App\Enums\Profile\ProfileEnum;
use App\Enums\ResponseCodeEnum;
use App\Http\Requests\ProfileAvatarRequest;
use App\Models\DescriptionProfile;
use App\Repository\Profile\ProfileRepository;
use App\Traits\ArrayHelper;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProfileService
 * @package App\Service\Profile
 */
class ProfileService
{
    use ArrayHelper;

    /** @var ProfileRepository */
    protected $profileRepository;

    /**
     * ProfileService constructor.
     * @param ProfileRepository $profileRepository
     */
    function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getAnotherUser(int $id)
    {
        $userData = $this->profileRepository->getUserData($id);
        if (!empty($userData)) {
            $userData->expNeed = DescriptionProfile::expGeneration($userData);
            $userData->last_online_at = date_create($userData->last_online_at)->Format('d.m.Y H:i');
            $userData->created_at = date_create($userData->created_at)->Format('d.m.Y H:i');
            $userData->gender == ProfileEnum::MALE ?
                $userData->genderName = trans('profile.male') :
                $userData->genderName = trans('profile.female');
            ArrayHelper::noAvatar($userData);
        }
        return $userData;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getCurrentUserData()
    {
        $userData = $this->profileRepository->getUserData();
        if ($userData) {
            $userData->expNeed = DescriptionProfile::expGeneration($userData);
            $userData->created_at = date_create($userData->created_at)->Format('d.m.y H:i');

            if (!is_null($userData->date_born)) {
                $userData->date_born = date_create($userData->date_born)->Format('d.m.Y');
            }

            if (!is_null($userData->about)) {
                $userData->about = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $userData->about);
            }

            $userData->gender == ProfileEnum::MALE ?
                $userData->genderName = trans('profile.male') :
                $userData->genderName = trans('profile.female');
            ArrayHelper::noAvatar($userData);
        }

        return $userData;
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserDataChange(int $userId)
    {
        $userData = $this->profileRepository->getUserData($userId);

        if ($userData) {
            ArrayHelper::noAvatar($userData);
        }
        return $userData;
    }

    /**
     * @param array $userData
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProfile(array $userData)
    {
        try {

            $userId = Auth::user()->id;
            if ($userId !== (INT)$userData['data_send']['user_id']) {
                throw new \Exception('Нельзя изменять данные чужого аккаунта!');
            }

            if (preg_match("/[\d]+/", $userData['data_send']['name'])) {
                throw new \Exception('Имя не должно содержать цифры!');
            }

            // If change gender
            if (Auth::user()->gender !== (INT)$userData['data_send']['gender']) {
                \App\User::query()
                    ->where('id', '=', $userId)
                    ->update(['gender' => (INT)$userData['data_send']['gender']]);
            }

            DescriptionProfile::updateOrCreate(
                ['user_id' => $userId],
                [
                    'real_name' => $userData['data_send']['name'],
                    'date_born' => $userData['data_send']['date_user'],
                    'town' => $userData['data_send']['town_user'],
                    'phone' => $userData['data_send']['phone'],
                    'about' => $userData['data_send']['about_user']
                ]
            );

            return response()->json(
                [
                    'status' => true,
                    'message' => 'OK'
                ]
            );

        } catch (\Exception $e) {

            return response()->json(
                [
                    'status' => false,
                    'errors'  =>  $e->getMessage(),
                ], ResponseCodeEnum::BAD_REQUEST
            );
        }
    }

    /**
     * @param ProfileAvatarRequest $request
     * @return mixed
     */
    public function changeAvatar(ProfileAvatarRequest $request)
    {
        if (!$request->hasFile('avatar')) {
            throw new \Exception('Error, file not found');
        }

        $avatarPath = Storage::disk('public')->putFile(
            ProfileEnum::USER_AVATAR_PATH, $request->avatar
        );

        $updateProfile = User::query()
            ->whereId(Auth::user()->id)
            ->update(['avatar' => $avatarPath]);

        if (!$updateProfile) {
            throw new \Exception('Error updating photo');
        }

        $request->session()->put('avatar', asset('storage/' . $avatarPath));
        if (file_exists($request->session()->get('avatar'))) {
            return true;
        }

        return false;
    }
}
