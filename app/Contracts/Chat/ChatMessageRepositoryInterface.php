<?php
namespace App\Contracts\Chat;

use App\Models\Chat\MessagesModel;

/**
 * Interface ChatMessageRepositoryInterface
 *
 * @package App\Contracts\Chat
 */
interface ChatMessageRepositoryInterface
{
    public function send(string $message, int $dialogId): int;

    public function edit(string $message, int $dialogId, int $messageId): MessagesModel;

    public function delete(int $dialogId, int $messageId): MessagesModel;
}
