<?php

namespace App\Repository\Chat;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ChatRepository
 *
 * @package App\Repository\Chat
 */
class ChatMessageRepository
{
    /**
     * Send message in dialog
     *
     * @param  $message  string
     * @param  $dialogId int
     *
     * @return int
     */
    public function sendMessage(string $message, int $dialogId): int
    {
        return DB::table('messages')->insertGetId([
            'dialog_id'  => $dialogId,
            'user_id'    => Auth::user()->id,
            'text'       => $message,
            'created_at' => now()
        ]);
    }
}
