<?php

namespace App\Service\Chat;

use App\Contracts\Chat\ChatMessageSearchInterface;
use App\DTO\Chat\SearchDTO;

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
     * @param string $search
     * @return
     */
    public function searchAll(SearchDTO $search)
    {
        return $this->chatSearchRepository->searchAll($search->text);
    }
}
