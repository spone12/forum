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
        return $this->profileRepository->getAnotherUser($id);
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserData()
    {
        return $this->profileRepository->getUserData();
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
