<?php

namespace App\Http\Controllers;

use App\Models\NotationModel;

class MapController extends Controller
{
    protected function view_map()
    {
        return view('map');
    }
}
