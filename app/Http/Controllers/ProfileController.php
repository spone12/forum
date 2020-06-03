<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\ProfileModel;

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
        return view('menu.profile.change_profile', ['data_user' => $id_user]);
    }
}
