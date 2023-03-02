<?php

namespace App\Http\Controllers;

use App\Models\Notation\NotationModel;

class MapController extends Controller
{
    protected function view_map()
    {
        return view('map');
    }
}
