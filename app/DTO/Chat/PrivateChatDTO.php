<?php

namespace App\DTO\Chat;

/**
 * Chat dialog DTO
 */
class PrivateChatDTO
{
    public function __construct(
        public int $dialogId,
        public int $partnerId,
        public \Illuminate\Contracts\Pagination\Paginator $messages
    ) {}
}
