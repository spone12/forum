<?php

namespace App\Http\Controllers;

use App\Enums\ResponseCodeEnum;
use App\Service\Profile\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileAvatarRequest;
use Validator;
use Illuminate\Http\Response;

/**
 * Class ProfileController
 *
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{

    /**
     * @var ProfileService
     */
    protected $profileService;

    /**
     * ProfileController constructor.
     *
     * @param ProfileService $profileService
     */
    function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @param  int $id
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function viewAnotherProfile(int $id)
    {
        $anotherUserData = $this->profileService->getAnotherUser($id);
        if (!empty($anotherUserData->name)) {
            return view('menu.Profile.profile', ['data_user' => $anotherUserData]);
        } else {
            return view('error_404', ['error' => ['Данного пользователя не существует']]);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function viewProfile()
    {
        $userData = $this->profileService->getCurrentUserData();
        return view('menu.Profile.profile', ['data_user' => $userData]);
    }

    /**
     * @param  int $userId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function changeProfile(int $userId)
    {
        $userData = $this->profileService->getUserDataChange($userId);
        return view('menu.Profile.changeProfile', ['data_user' => $userData]);
    }

    /**
     * @param Request $request
     * @return array|Response|void
     */
    public function changeProfileConfirm(Request $request)
    {
        try {
            if (!$request->ajax()) {
                throw new \Exception('This is not JSON request!');
            }

            $dataToChange = $request->only(['data_send']);
            $dataChangeResponse = $this->profileService->changeProfile($dataToChange);

            if (!$dataChangeResponse->original['status']) {
                throw new \Exception($dataChangeResponse->original['errors']);
            }
        } catch(\Exception $e) {
            return new Response(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseCodeEnum::SERVER_ERROR
            );
        }

        return ['data_user' => $dataChangeResponse];

    }

    /**
     * @param  ProfileAvatarRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeAvatar(ProfileAvatarRequest $request)
    {
        $isChanged = $this->profileService->changeAvatar($request);
        return redirect()->route('profile_id', Auth::user()->id)
            ->with('success', 'Вы успешно изменили аватар')
            ->with('isChanged', $isChanged);
    }
}
