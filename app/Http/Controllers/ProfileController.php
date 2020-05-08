<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\ProfileModel;

class ProfileController extends Controller
{
    public function view_profile(Request $request)
    {
        $data_user = ProfileModel::get_data_user();

        return view('menu.profile', ['data_user' => $data_user]);
    }
}
