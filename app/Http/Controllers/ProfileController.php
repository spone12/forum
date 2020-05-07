<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\ProfileModel;

class ProfileController extends Controller
{
    public function view_profile(Request $request)
    {
        return view('menu.profile');
    }
}
