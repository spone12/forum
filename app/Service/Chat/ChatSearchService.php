<?php

namespace App\Service\Chat;

use App\Contracts\Chat\ChatMessageSearchInterface;

/**
 * Chat search service class
 */
class ChatSearchService
{
    /** @var ChatMessageSearchInterface $chatSearchRepository */
    protected $chatSearchRepository;

    /**
     * ChatSearchRepository constructor.
     *
     * @param ChatMessageSearchInterface $chatSearchRepository
     */
    function __construct(ChatMessageSearchInterface $chatSearchRepository) {
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
