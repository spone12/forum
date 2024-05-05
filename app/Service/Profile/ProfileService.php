<?php

namespace App\Service\Profile;

use App\Enums\Profile\ProfileEnum;
use App\Http\Requests\ProfileAvatarRequest;
use App\Models\DescriptionProfile;
use App\Repository\Profile\ProfileRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ProfileService
 * @package App\Service\Profile
 */
class ProfileService
{
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
        $userData = $this->profileRepository->getAnotherUserData($id);
        if (!empty($userData)) {
            $userData->expNeed = DescriptionProfile::expGeneration($userData);
            $userData->last_online_at = date_create($userData->last_online_at)->Format('d.m.Y H:i');
            $userData->created_at = date_create($userData->created_at)->Format('d.m.Y H:i');
            $userData->gender == ProfileEnum::MALE ?
                $userData->genderName = trans('profile.male') :
                $userData->genderName = trans('profile.female');

            if (is_null($userData->avatar)) {
                $userData->avatar = ProfileEnum::NO_AVATAR;
            }
        }
        return $userData;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getCurrentUserData()
    {
        $userData = $this->profileRepository->getCurrentUserData();
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

            if (is_null($userData->avatar)) {
                $userData->avatar = ProfileEnum::NO_AVATAR;
            }
        }

        return $userData;
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserDataChange(int $userId)
    {
        return $this->profileRepository->getUserDataChange($userId);
    }

    /**
     * @param $input
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function changeProfile($input)
    {
        return $this->profileRepository->changeProfile($input);
    }

    /**
     * @param ProfileAvatarRequest $request
     * @return mixed
     */
    public function changeAvatar(ProfileAvatarRequest $request)
    {
        return $this->profileRepository->changeAvatar($request);
    }
}
