<?php

namespace App\Repository\Chat;

use App\Enums\Chat\DialogType;
use App\Models\Chat\DialogModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
     * Search word in the chats
     *
     * @param  $word string
     * @return Collection
     */
    public function search(string $word, $limit = 10): Collection
    {
        return DB::table('dialogs')
            ->select('messages.send', 'dialogs.dialog_id', 'messages.created_at', 'messages.text')
            ->join('users', 'dialogs.recive', '=', 'users.id')
            ->join('users as user2', 'dialogs.send', '=', 'user2.id')
            ->leftJoin('messages', 'messages.dialog_id', '=', 'dialogs.dialog_id')
            ->where(
                function ($query) {
                    $query->where('dialogs.recive', Auth::user()->id)
                        ->orWhere('dialogs.send', Auth::user()->id);
                }
            )
            ->where(
                function ($query) use (&$word) {
                    $query->where('messages.text', 'like', '%' . $word . '%')
                        ->orWhere('users.name', 'like', '%'. $word .'%')
                        ->orWhere('user2.name', 'like', '%'. $word .'%');
                }
            )
            ->whereNull('messages.deleted_at')
            //->groupBy('users.id')
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('users.name', 'ASC')
            ->orderBy('user2.name', 'ASC')
            ->limit($limit)
            ->get();
    }

    /**
     * Send message in dialog
     *
     * @param  $message  string
     * @param  $dialogId int
     * @param  $userId   int
     *
     * @return int
     */
    public function sendMessage(string $message, int $dialogId, int $userId): int
    {
        return DB::table('messages')->insertGetId([
            'dialog_id'  => $dialogId,
            'send'       => Auth::user()->id,
            'user_id'    => Auth::user()->id,
            'recive'     => $userId,
            'text'       => $message,
            'created_at' => now()
        ]);
    }

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
                'm.send',
                'm.recive',
                'm.text'
            )
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
