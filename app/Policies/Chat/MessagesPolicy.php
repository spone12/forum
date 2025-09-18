<?php

namespace App\Policies\Chat;

use App\Enums\ResponseCodeEnum;
use App\Models\Chat\MessagesModel;
use App\User;
use Illuminate\Auth\Access\Response;

class MessagesPolicy
{
    /**
     * @param User $user
     * @param int $message
     * @return bool
     */
    public function messageAccess(User $user, MessagesModel $message):Response
    {
        $dialog = $message->dialog;
        $userInDialog = $dialog->participants()->where('user_id', $user->id)->exists();

        return ($user->id === $message->user_id && $userInDialog)
            ? Response::allow()
            : Response::deny('You cannot access this action!', ResponseCodeEnum::FORBIDDEN);
    }
}
