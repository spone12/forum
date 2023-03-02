<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileAvatarRequest;
use Validator;

/**
 * Class ProfileController
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function viewAnotherProfile(int $id)
    {
        $userData = ProfileModel::getAnotherUser($id);

        if (!empty($userData->name)) {
            return view('menu.profile', ['data_user' => $userData]);
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

        $userData = ProfileModel::getUserData();
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

        $userData = ProfileModel::getUserDataChange($userId);
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
            $back = ProfileModel::changeProfile($input);

            try
            {
                if (!$back->original['status']) {
                    throw new \Exception($back->original['errors']);
                }
            } catch(\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
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
        $isChanged = ProfileModel::changeAvatar($request);

        return redirect()->route('profile_id', Auth::user()->id)
            ->with('success', 'Вы успешно изменили аватар')
            ->with('isChanged', $isChanged);

    }
}
