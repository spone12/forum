<?php

namespace App\Policies\Chat;

use App\User;
use App\Models\Chat\DialogModel as DialogModel;
use Illuminate\Auth\Access\Response;

class DialogPolicy
{
    /**
     * @param User $user
     * @param DialogModel $dialog
     * @return bool
     */
    public function dialogAccess(User $user, DialogModel $dialog):Response
    {
        $dialogExists = DialogModel::with('participants')
            ->where('dialog_id', $dialog->dialog_id)
            ->where('type', $dialog->type)
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->exists();

        if (!$dialogExists) {
            return Response::deny('You cannot access this action!');
        }

        return Response::allow();
    }
}
