<?php

namespace App\Service\Profile;

use App\Http\Requests\ProfileAvatarRequest;
use App\Repository\Profile\ProfileRepository;

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
    function __construct(ProfileRepository $profileRepository) {

        $this->profileRepository = $profileRepository;
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getAnotherUser(int $id) {

        $anotherUserData = $this->profileRepository->getAnotherUser($id);
        return $anotherUserData;
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserData() {

        $userData = $this->profileRepository->getUserData();
        return $userData;
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserDataChange(int $userId) {

        $userData = $this->profileRepository->getUserDataChange($userId);
        return $userData;
    }

    /**
     * @param $input
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function changeProfile($input) {

        $userData = $this->profileRepository->changeProfile($input);
        return $userData;
    }

    /**
     * @param ProfileAvatarRequest $request
     * @return mixed
     */
    public function changeAvatar(ProfileAvatarRequest $request) {

        $userData = $this->profileRepository->changeAvatar($request);
        return $userData;
    }
}
