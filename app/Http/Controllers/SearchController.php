<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\SearchModel;

class SearchController extends Controller
{

    public function getDataSearch(Request $request)
    {
        $search = $request->only(['search', 'search-by']);

        if(stripos($search['search-by'], 'user' ) !== false)
        {
            $result = SearchModel::search_by_user((object)$search);
            $result->search_by = 1;
        }
        else
        {
             $result = SearchModel::search_by_notation((object)$search);
             $result->search_by = 2;
        }

        return view('search',  ['result' => $result]);
    }
}
