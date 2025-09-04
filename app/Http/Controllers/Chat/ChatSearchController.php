<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SearchMessageRequest;
use App\Http\Resources\Chat\ChatSearchResource;
use App\Http\Resources\{ErrorResource, SuccessResource};
use App\Service\Chat\ChatSearchService;
use App\DTO\Chat\SearchDTO;

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
     * Controller for searching messages in all dialogs
     *
     * @param SearchMessageRequest $request
     * @return ErrorResource|SuccessResource
     */
    #[Route('/chat/search_all/', methods: ['GET'])]
    protected function searchAll(SearchMessageRequest $request)
    {
        try {
            $validated = $request->validated();
            $dto = new SearchDTO($validated['text']);

            return new SuccessResource(
                new ChatSearchResource(
                    $this->chatSearchService->searchAll($dto)
                )
            );
        } catch (\Throwable $exception) {
            return new ErrorResource();
        }
    }
}
