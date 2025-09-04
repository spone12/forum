<?php

namespace App\Broadcasting;

use App\Enums\Chat\DialogType;
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
        return $user->dialogParticipants()
            ->where('dialogs.dialog_id', $dialogId)
            ->where('type', DialogType::PRIVATE)
            ->exists();
    }
}
