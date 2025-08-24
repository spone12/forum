<?php

namespace App\Broadcasting;

use App\Models\Chat\MessagesModel;
use App\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param int $dialogId
     *
     * @return bool
     */
    public function join(User $user, int $dialogId)
    {
        $messageObj = MessagesModel::select('user_id')
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        return auth()->check() && (auth()->id() === $messageObj->user_id);
    }
}
