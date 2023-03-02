<?php

namespace App\Http\Controllers;

use App\Models\Notation\NotationModel;

/**
 * Class MapController
 * @package App\Http\Controllers
 */
class MapController extends Controller
{
    /**
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function viewMap()
    {
        return view('map');
    }
}
