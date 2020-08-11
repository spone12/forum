<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\NotationModel;
use App\Http\Requests\NotationRequest;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    protected function view_map()
    {
        return view('map');
    }
}
