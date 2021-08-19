<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\ProfileModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileAvatarRequest;
use Validator;

class ProfileController extends Controller
{
    
    public function viewAnotherProfile(int $id)
    {
            $dataUser = ProfileModel::getAnotherUser($id);
            
        if(!empty($dataUser->name))
            return view('menu.profile', ['data_user' => $dataUser]);
            
        else  return view('error_404', ['error' => ['Данного пользователя не существует']]);
    }

    public function viewProfile(Request $request)
    {
        $dataUser = ProfileModel::getDataUser();

        return view('menu.profile', ['data_user' => $dataUser]);
    }

    public function changeProfile(int $id_user)
    {
        $dataUser = ProfileModel::getUserDataChange($id_user);

        return view('menu.profile.change_profile', ['data_user' => $dataUser]);
    }

    public function changeProfileConfirm(Request $request)
    {
        if($request->ajax())
        {
            $input = $request->only(['data_send']);

           /* $validator = Validator::make($input, 
                [
                     $input['data_send']['gender'] => 'numeric',
                ])->validate();
                
            if($validator)
            {*/
        
                
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
           // }
          
        }
    }

    public function changeAvatar(ProfileAvatarRequest $request)
    {
        $answer = ProfileModel::changeAvatar($request);
   
        return redirect()->route('profile_id', Auth::user()->id)
            ->with('success','Вы успешно изменили аватар');
           
    }
}
