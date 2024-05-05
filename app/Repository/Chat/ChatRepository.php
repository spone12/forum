<?php

namespace App\Repository\Chat;

use App\Models\Chat\DialogModel as DialogModel;
use App\User;
use Carbon\Carbon;
use App\Models\Chat\MessagesModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\Profile\ProfileEnum;
use \Illuminate\Support\Str;

/**
 * Class ChatRepository
 *
 * @package App\Repository\Chat
 */
class ChatRepository
{
    /**
     * Get current user's dialogs
     *
     * @param  int $limit
     * @return Collection
     */
    public function getUserChats(int $limit = 0): Collection
    {
        $userDialogs = DB::table('dialog')
            ->select('dialog_id', 'send', 'recive')
            ->where(
                function ($query) {
                    $query->where('dialog.send', Auth::user()->id)
                        ->orWhere('dialog.recive', Auth::user()->id);
                }
            )
            ->get();

        if ($limit) {
            $userDialogs = $userDialogs->take($limit);
        }

        foreach ($userDialogs as $k => $chat) {
            $lastMessage = MessagesModel::query()
                ->where('dialog', $chat->dialog_id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'DESC')
            ->first();

            if (is_null($lastMessage)) {
                unset($userDialogs[$k]);
                continue;
            }

            $dialogWithId = (Auth::user()->id == $chat->send) ? $chat->recive : $chat->send;
            $user = DB::table('users')
                ->select('users.id AS userId', 'users.name',  'users.avatar')
                ->where('users.id', $dialogWithId)
                ->first();

            $userDialogs[$k]->id = $user->userId;
            $userDialogs[$k]->name = $user->name;
            $userDialogs[$k]->avatar = $user->avatar;
            $userDialogs[$k]->text = $lastMessage->text;
            $userDialogs[$k]->created_at = $lastMessage->created_at;
            $userDialogs[$k]->isRead = $lastMessage->read;
            $userDialogs[$k]->difference =
                Carbon::createFromFormat('Y-m-d H:i:s', $lastMessage->created_at)->diffForHumans();

            $this->checkAvatarExist($userDialogs[$k]);

            if (strlen($userDialogs[$k]->text) >= 50) {
                $chat->text = str_ireplace(['</div>'], '<br>', $chat->text);
                $userDialogs[$k]->text = Str::limit(strip_tags($chat->text, '<br>'), 50);
            }
        }
        return $userDialogs->sortByDesc('created_at');
    }

    /**
     * Search word in the chats
     *
     * @param  $word string
     * @return Collection
     */
    public function search(string $word, $limit = 10): Collection
    {

        return DB::table('dialog')
            ->select('messages.send', 'dialog.dialog_id', 'messages.created_at', 'messages.text')
            ->join('users', 'dialog.recive', '=', 'users.id')
            ->join('users as user2', 'dialog.send', '=', 'user2.id')
            ->leftJoin('messages', 'messages.dialog', '=', 'dialog.dialog_id')
            ->where(
                function ($query) {
                    $query->where('dialog.recive', Auth::user()->id)
                        ->orWhere('dialog.send', Auth::user()->id);
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
        return DB::table('messages')->insertGetId(
            [
                'dialog'     => $dialogId,
                'send'       =>  Auth::user()->id,
                'recive'     => $userId,
                'text'       => $message,
                'created_at' => Carbon::now()
            ]
        );
    }

    /**
     * Edit message in dialog
     *
     * @param  $message   string
     * @param  $dialogId  int
     * @param  $messageId int
     * @return array
     */
    public function editMessage(string $message, int $dialogId, int $messageId)
    {
        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $messageObj->text = $message;

        if (!$messageObj->save()) {
            throw new \Exception('Message not edited!');
        }

        return [
            'success' => true
        ];
    }

    /**
     * Delete message in dialog
     *
     * @param  $dialogId  int
     * @param  $messageId int
     * @return array
     */
    public function deleteMessage(int $dialogId, int $messageId)
    {
        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::query()->where('message_id', $messageId)->firstOrFail();
        $messageObj->delete();

        if (!$messageObj->save()) {
            throw new \Exception('Message not deleted!');
        }

        return [
            'success' => true
        ];
    }

    /**
     * Recover message in dialog
     *
     * @param  $dialogId  int
     * @param  $messageId int
     * @return array
     */
    public function recoverMessage(int $dialogId, int $messageId)
    {
        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::onlyTrashed()
            ->where('message_id', $messageId)
            ->firstOrFail();
        $messageObj->restore();

        if (!$messageObj->save()) {
            throw new \Exception('Message not recovered!');
        }

        return [
            'success' => true
        ];
    }

    /**
     * Get dialog Id or create new
     *
     * @param  $userId   int
     * @param  $dialogId int
     * @return int
     */
    public function getDialogId(int $userId, int $dialogId = 0): int
    {
        if ($dialogId == 0) {

            $dialogExist = DB::table('dialog AS d')
                ->select('d.dialog_id')
                ->where(
                    function ($query) use ($userId) {
                        $query->where('d.send',  Auth::user()->id)
                            ->where('d.recive', $userId);
                    }
                )
                ->orWhere(
                    function ($query) use ($userId) {
                        $query->where('d.send',  $userId)
                            ->where('d.recive', Auth::user()->id);
                    }
                )
                ->first();

        } else {
            $dialogExist = DialogModel::where('dialog_id', $dialogId)->first();
        }

        if (empty($dialogExist) || is_null($dialogExist)) {

            $dialogId = DB::table('dialog')->insertGetId(
                [
                'send' =>  Auth::user()->id,
                'recive' => $userId
                ]
            );
        } else {
            $dialogId = $dialogExist->dialog_id;
        }

        return $dialogId;
    }

    /**
     * Get user dialog by ID
     *
     * @param  $userId   int
     * @param  $dialogId int
     * @param  $message  string
     * @return array
     */
    public function getUserDialog(int $dialogId, $userMessageWithId = 0): array
    {
        $currentUserId = Auth::user()->id;
        $dialogCheck = DialogModel::where('dialog_id', $dialogId);

        if (!$dialogCheck->exists()
            || ($dialogCheck->first()->send != $currentUserId && $dialogCheck->first()->recive != $currentUserId)
        ) {
            throw new \Exception('Chat not exist');
        }

        $currentUserAvatar = Auth::user()->avatar ?: ProfileEnum::NO_AVATAR;
        $dialogMessages = DB::table('messages')
            ->select(
                'messages.message_id', 'messages.text', 'messages.dialog', 'messages.created_at',
                'messages.updated_at', 'messages.send', 'messages.recive',
                'messages.text'
            )
            ->where('dialog', $dialogId)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        if ($dialogMessages->count()) {
            // Get id of the user we are talking to
            $anotherUserId = ($dialogMessages[0]->send == $currentUserId) ?
                $dialogMessages[0]->recive :
                $dialogMessages[0]->send;

            $anotherUserObj = User::where('id', $anotherUserId)->first();
            $this->checkAvatarExist($anotherUserObj);

            foreach ($dialogMessages as $dialog) {

                $dialog->text = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $dialog->text);
                $dialog->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $dialog->created_at)->diffForHumans();

                $this->formatChatDate($dialog);
                if ($dialog->send == $currentUserId) {
                    $dialog->name = Auth::user()->name;
                    $dialog->avatar = $currentUserAvatar;
                    $dialog->id = $currentUserId;
                } else {
                    $dialog->name = $anotherUserObj->name;
                    $dialog->avatar = $anotherUserObj->avatar;
                    $dialog->id = $anotherUserObj->id;
                }
            }
        } else {
            $anotherUserId = $userMessageWithId;
        }

        return [
            'dialogMessages' => $dialogMessages,
            'dialogId' => $dialogId,
            'recive' => $anotherUserId
        ];
    }

    /**
     * Formate create date message value
     *
     * @param  $obj
     * @param  $currentDate string
     * @return void
     */
    private function formatChatDate($obj, $currentDate = '')
    {
        $chatDate = Carbon::parse($obj->created_at);
        if (empty($currentDate)) {
            $currentDate = Carbon::now()->format('d.m.Y');
        }

        if ($currentDate == $chatDate->format('d.m.Y')) {
            $obj->created_at =  $chatDate->format('H:i');
        } else {
            $obj->created_at = $chatDate->format('d.m.Y H:i');
        }
    }

    /**
     * If empty avatar then set No avatar IMG
     *
     * @param  $obj
     * @return void
     */
    private function checkAvatarExist($obj)
    {
        if (!$obj->avatar) {
            $obj->avatar = ProfileEnum::NO_AVATAR;
        }
    }

    /**
     * Check access to dialog
     *
     * @param  int $dialogId
     * @throws \Exception
     */
    private function checkAccess(int $dialogId)
    {
        $dialog = DialogModel::query()->where('dialog_id', $dialogId)->first();
        if (!$dialog->exists()) {
            throw new \Exception('Dialog not found!');
        }

        if ($dialog->send !== Auth::user()->id && $dialog->recive !== Auth::user()->id) {
            throw new \Exception('Not access to message edit!');
        }
    }
}
