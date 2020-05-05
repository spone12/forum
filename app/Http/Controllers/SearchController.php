<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function getDataSearch(Request $search)
    {
        return view('search',  ['name' => $search->input('search')]);
    }
}
