<?php

namespace App\DTO\Chat;

/**
 * Search DTO
 */
class SearchDTO
{
    public function __construct(
        public readonly string $text
    ) {}
}
