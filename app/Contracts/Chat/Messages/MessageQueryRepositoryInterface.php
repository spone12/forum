<?php
namespace App\Contracts\Chat\Messages;

use Illuminate\Contracts\Pagination\Paginator;

/**
 * Interface MessageQueryRepositoryInterface
 *
 * @package App\Contracts\Chat\Messages
 */
interface MessageQueryRepositoryInterface
{
    public function getDialogMessages(int $dialogId, int $messagesPerPage = 10): Paginator;
}
