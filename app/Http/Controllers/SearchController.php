<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SearchController extends Controller
{

    public function getDataSearch(Request $search)
    {
        return view('search',  ['name' => $search->input('search')]);
    }
}
