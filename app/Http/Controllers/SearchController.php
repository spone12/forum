<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\SearchService;

class SearchController extends Controller
{

    /** @var SearchService */
    protected $searchService;

    /**
     * SearchController constructor.
     * @param SearchService $searchService
     */
    function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    public function getDataSearch(Request $request)
    {

        $search = $request->only(['search', 'search-by']);
        if (stripos($search['search-by'], 'user' ) !== false) {
            $result = $this->searchService->byUser((object)$search);
            $result->search_by = 1;
        } else {
             $result = $this->searchService->byNotation((object)$search);
             $result->search_by = 2;
        }

        return view('search',  ['result' => $result]);
    }
}
