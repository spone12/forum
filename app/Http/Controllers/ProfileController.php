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
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{

    /** @var ProfileService */
    protected $profileService;

    /**
     * ProfileController constructor.
     * @param ProfileService $profileService
     */
    function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function viewAnotherProfile(int $id)
    {

        $anotherUserData = $this->profileService->getAnotherUser($id);
        if (!empty($anotherUserData->name)) {
            return view('menu.profile', ['data_user' => $anotherUserData]);
        } else {
            return view('error_404', ['error' => ['Данного пользователя не существует']]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function viewProfile(Request $request)
    {

        $userData = $this->profileService->getUserData();
        return view('menu.profile', ['data_user' => $userData]);
    }

    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function changeProfile(int $userId)
    {

        $userData = $this->profileService->getUserDataChange($userId);
        return view('menu.profile.change_profile', ['data_user' => $userData]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function changeProfileConfirm(Request $request)
    {

        if ($request->ajax()) {
            $input = $request->only(['data_send']);
            $back = $this->profileService->changeProfile($input);

            try
            {
                if (!$back->original['status']) {
                    throw new \Exception($back->original['errors']);
                }
            } catch(\Exception $e) {
                return new Response([
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseCodeEnum::SERVER_ERROR);
            }

            return array('data_user' => $back);
        }
    }

    /**
     * @param ProfileAvatarRequest $request
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
