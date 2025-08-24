<?php

namespace App\Repository\Chat;

use App\Enums\Chat\DialogType;
use App\Enums\Profile\ProfileEnum;
use App\Models\Chat\DialogModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ChatRepository
 *
 * @package App\Repository\Chat
 */
class ChatRepository
{
    /**
     * Get dialog messages by id
     *
     * @param int $dialogId
     * @return
     */
    public function getDialogMessages(int $dialogId)
    {
        return DB::table('messages AS m')
            ->select(
                'm.message_id',
                'm.text',
                'm.dialog_id',
                'm.created_at',
                'm.updated_at',
                'm.user_id',
                'u.name',
                DB::raw(
                'CASE WHEN u.avatar IS NULL THEN
                        "' . ProfileEnum::NO_AVATAR . '"
                     ELSE
                        u.avatar
                     END as avatar'
                ),
                'u.id',
                'm.text'
            )
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->where('dialog_id', $dialogId)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);
    }

    /**
     * Get user dialog
     *
     * @param int $userId
     * @return
     */
    public function getUserDialog(int $userId, DialogType $dialogType)
    {
        return DialogModel::query()
            ->select('dialog_id')
            ->where('type', $dialogType)
            ->whereHas('participants', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();
    }
}
