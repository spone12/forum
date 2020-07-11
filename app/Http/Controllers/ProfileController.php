<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\ProfileModel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileAvatarRequest;
use Validator;

class ProfileController extends Controller
{
    
    public function view_another_profile(int $id)
    {
            $data_user = ProfileModel::get_another_user($id);
            
        if(!empty($data_user->name))
            return view('menu.profile', ['data_user' => $data_user]);
        else  return view('error_404', ['error' => ['Данного пользователя не существует']]);
    }

    public function view_profile(Request $request)
    {
        $data_user = ProfileModel::get_data_user();

        return view('menu.profile', ['data_user' => $data_user]);
    }

    public function change_profile(int $id_user)
    {
        $data_user = ProfileModel::get_user_data_change($id_user);

        return view('menu.profile.change_profile', ['data_user' => $data_user]);
    }

    public function change_profile_confirm(Request $request)
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
                
               
                $back = ProfileModel::change_profile($input);
                return array('data_user' => $back);
           // }
          
        }
    }

    public function change_avatar(ProfileAvatarRequest $request)
    {
        $answer = ProfileModel::change_avatar($request);
   
        return redirect()->route('profile_id', Auth::user()->id)
            ->with('success','Вы успешно изменили аватар');
           
    }
}
