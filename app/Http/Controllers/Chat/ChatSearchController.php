<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Service\Chat\ChatSearchService;
use Illuminate\Http\Request;

/**
 * Class ChatSearchController
 *
 * @package App\Http\Controllers
 */
class ChatSearchController extends Controller
{
    /**
     * @var ChatSearchService $chatSearchService
     */
    protected $chatSearchService;

    /**
     * ChatSearchService constructor.
     *
     * @param ChatSearchService $chatSearchService
     */
    function __construct(ChatSearchService $chatSearchService)
    {
        $this->chatSearchService = $chatSearchService;
    }

    /**
     * Controller search chat
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[Route('/chat/search_all/', methods: ['GET'])]
    protected function searchAll(Request $request)
    {
        $searchText = $request->input('searchText');
        $data = $this->chatSearchService->searchAll($searchText);
        return response()->json(['searchResult' => $data]);
    }
}
