<?php

namespace App\DTO\Chat;

use \Illuminate\Contracts\Pagination\Paginator;

/**
 * Chat dialog DTO
 */
class PrivateChatDTO
{
    public function __construct(
        public int $dialogId,
        public Paginator $messages
    ) {}
}
