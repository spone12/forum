<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\SearchModel;

class SearchController extends Controller
{

    public function getDataSearch(Request $search)
    {
        return view('search',  ['name' => $search->input('search')]);
    }
}
