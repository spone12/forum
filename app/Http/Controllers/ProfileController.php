<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileAvatarRequest;
use Validator;

class ProfileController extends Controller
{

    public function viewAnotherProfile(int $id)
    {
        $userData = ProfileModel::getAnotherUser($id);

        if(!empty($userData->name))
            return view('menu.profile', ['data_user' => $userData]);
        else
            return view('error_404', ['error' => ['Данного пользователя не существует']]);
    }

    public function viewProfile(Request $request)
    {
        $userData = ProfileModel::getUserData();

        return view('menu.profile', ['data_user' => $userData]);
    }

    public function changeProfile(int $userId)
    {
        $userData = ProfileModel::getUserDataChange($userId);

        return view('menu.profile.change_profile', ['data_user' => $userData]);
    }

    public function changeProfileConfirm(Request $request)
    {
        if($request->ajax())
        {
            $input = $request->only(['data_send']);
            $back = ProfileModel::changeProfile($input);

            try
            {
                if(!$back->original['status'])
                {
                    throw new \Exception($back->original['errors']);
                }
            }
            catch(\Exception $e)
            {
                die($e->getMessage());
            }

            return array('data_user' => $back);
        }
    }

    public function changeAvatar(ProfileAvatarRequest $request)
    {
        $isChanged = ProfileModel::changeAvatar($request);

        return redirect()->route('profile_id', Auth::user()->id)
            ->with('success','Вы успешно изменили аватар')
            ->with('isChanged', $isChanged);

    }
}
