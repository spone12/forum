<?php
namespace App\Contracts\Chat;

use Illuminate\Support\Collection;

/**
 * Interface ChatMessageSearchInterface
 *
 * @package App\Contracts\Chat
 */
interface ChatMessageSearchInterface
{
    public function searchAll(string $searchText, int $limit = 10): Collection;
}
