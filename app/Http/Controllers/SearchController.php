<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\SearchModel;

class SearchController extends Controller
{

    public function getDataSearch(Request $request)
    {
        $search = $request->input('search');
        $result = SearchModel::query_search_user($search);

        return view('search',  ['result' => $result]);
    }
}
