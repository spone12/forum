<?php

namespace App\Service\Chat;

use App\Repository\Chat\ChatSearchRepository;

/**
 * Chat search service class
 */
class ChatSearchService
{
    /** @var ChatSearchRepository $chatSearchRepository */
    protected $chatSearchRepository;

    /**
     * ChatSearchRepository constructor.
     *
     * @param ChatSearchRepository $chatSearchRepository
     */
    function __construct(ChatSearchRepository $chatSearchRepository) {
        $this->chatSearchRepository = $chatSearchRepository;
    }

    /**
     * Search messages in all dialogs
     *
     * @param string $searchText
     * @return
     */
    public function searchAll(string $searchText)
    {
        return $this->chatSearchRepository->searchAll($searchText);
    }
}
