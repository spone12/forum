<?php

namespace App\Policies;

use App\User;
use App\Models\Chat\DialogModel as DialogModel;
use Illuminate\Auth\Access\Response;

class ChatPolicy
{
    /**
     * @param User $user
     * @param DialogModel $dialog
     * @return bool
     */
    public function access(User $user, DialogModel $dialog):Response
    {
        if (!$dialog) {
            return Response::deny('Dialog not found!');
        }

        if ($dialog->send === $user->id || $dialog->recive === $user->id) {
            return Response::allow();
        }

        return Response::deny('You cannot access this action!');
    }
}
