<?php
namespace App\Contracts\Chat\Messages;

use App\Models\Chat\MessagesModel;

/**
 * Interface ChatMessageRepositoryInterface
 *
 * @package App\Contracts\Chat
 */
interface MessageCommandRepositoryInterface
{
    public function send(string $message, int $dialogId): int;

    public function edit(string $message, int $dialogId, int $messageId): MessagesModel;

    public function delete(int $dialogId, int $messageId): MessagesModel;

    public function recover(int $dialogId, int $messageId): MessagesModel;
}
