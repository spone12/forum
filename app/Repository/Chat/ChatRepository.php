<?php

namespace App\Repository\Chat;

use App\Models\Chat\DialogModel as DialogModel;
use App\User;
use Carbon\Carbon;
use App\Models\Chat\ChatModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\Profile\ProfileEnum;
use \Illuminate\Support\Str;

class ChatRepository
{
    /**
     * Get current user's dialogs
     * @param int $limit
     * @return Collection
     */
    public function getUserChats(int $limit = 0): Collection {

        $userDialogs = DB::table('dialog')
            ->select('dialog_id', 'send', 'recive')
            ->where(function($query)
            {
                $query->where('dialog.send', Auth::user()->id)
                    ->orWhere('dialog.recive', Auth::user()->id);
            })
            ->get();

        if ($limit) {
            $userDialogs = $userDialogs->take($limit);
        }

        foreach ($userDialogs as $k => $chat) {

            $lastMessage = ChatModel::where('dialog', $chat->dialog_id)->orderBy('created_at', 'DESC')->first();
            if(is_null($lastMessage)) {
                unset($userDialogs[$k]);
                continue;
            }

            $dialogWithId = (Auth::user()->id == $chat->send) ? $chat->recive : $chat->send;
            $user = DB::table('users')
                ->select('users.id AS userId', 'users.name',  'users.avatar')
                ->where( 'users.id', $dialogWithId)
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

            if(strlen($lastMessage->text) >= 50) {
                $lastMessage->text = Str::limit($chat->text, 50);
            }
        }

        return $userDialogs->sortByDesc('created_at');
    }

    /**
     * search word in the chats
     * @param $word string
     * @return Collection
     */
    public function search(string $word, $limit = 10): Collection {

        $searchResult = DB::table('dialog')
            ->select(  'messages.send','dialog.dialog_id', 'messages.created_at','messages.text')
            ->join('users', 'dialog.recive', '=', 'users.id')
            ->join('users as user2', 'dialog.send', '=', 'user2.id')
            ->leftJoin('messages','messages.dialog', '=', 'dialog.dialog_id' )
            ->where(function($query)
            {
                $query->where('dialog.recive', Auth::user()->id)
                    ->orWhere('dialog.send', Auth::user()->id);
            })
            ->where(function($query) use (&$word)
            {
                $query->where('messages.text', 'like', '%' . $word . '%')
                    ->orWhere('users.name', 'like', '%'. $word .'%')
                    ->orWhere('user2.name', 'like', '%'. $word .'%');
            })
            //->groupBy('users.id')
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('users.name', 'ASC')
            ->orderBy('user2.name', 'ASC')
            ->limit($limit)
            ->get();

        foreach($searchResult as $search) {

            $userObj = User::where('id', $search->send)->first();

            $search->avatar = $userObj->avatar ?: ProfileEnum::NO_AVATAR;
            $search->id = $userObj->id;
            $search->name = $userObj->name;
        }

        return $searchResult;
    }

    /**
     * Send message in dialog
     * @param $message string
     * @param $dialogId int
     * @param $userId int
     * @return array
     */
    public function sendMessage(string $message, int $dialogId, int $userId) {

        $dialogId = $this->getDialogId($userId, $dialogId);
        try {

            $messageId = DB::table('messages')->insertGetId(
                [
                    'dialog' => $dialogId,
                    'send' =>  Auth::user()->id,
                    'recive' => $userId,
                    'text' => $message,
                    'created_at' => Carbon::now()
                ]);
        }
        catch (\Exception $exception) {
            #### repair
            return $exception->getMessage();
        }

        return [
            'messageId' => $messageId,
            'created_at' =>  Carbon::now()->format('H:i'),
            'avatar' => session('avatar'),
            'name' => Auth::user()->name,
            'userId' => Auth::user()->id
        ];
    }

    /**
     * Get dialog Id or create new
     * @param $userId int
     * @param $dialogId int
     * @return int
     */
    public function getDialogId($userId, $dialogId = 0): int {

        if ($dialogId == 0) {

            $dialogExist = DB::table('dialog AS d')
                ->select('d.dialog_id')
                ->where(function($query) use ($userId)
                {
                    $query->where('d.send',  Auth::user()->id)
                        ->where('d.recive', $userId);
                })
                ->orWhere(function($query) use ($userId)
                {
                    $query->where('d.send',  $userId)
                        ->where('d.recive', Auth::user()->id);
                })
                ->first();
        }
        else {
            $dialogExist = DialogModel::where('dialog_id', $dialogId)->exists();
        }

        if(empty($dialogExist) || $dialogExist == false) {

            $dialogId = DB::table('dialog')->insertGetId([
                'send' =>  Auth::user()->id,
                'recive' => $userId
            ]);
        }

        return $dialogId;
    }

    /**
     * get user dialog by ID
     * @param $userId int
     * @param $dialogId int
     * @param $message string
     * @return array
     */
    public function getUserDialog(int $dialogId, $userMessageWithId = 0): array {

        $currentUserId =  Auth::user()->id;
        $dialogCheck = DialogModel::where('dialog_id', $dialogId);

        if (!$dialogCheck->exists()) {
            return ['error' => 'Chat not exist'];
        } else {

            if ($dialogCheck->first()->send != $currentUserId && $dialogCheck->first()->recive != $currentUserId) {
                return ['error' => 'Chat not exist'];
            }
        }

        $currentUserAvatar = Auth::user()->avatar ?: ProfileEnum::NO_AVATAR;

        $dialogMessages = DB::table('messages')
            ->select( 'messages.text', 'messages.dialog', 'messages.created_at',
                'messages.updated_at', 'messages.send', 'messages.recive',
                'messages.text' )
            ->where('dialog', $dialogId)
            ->orderBy('created_at', 'asc')
            ->get();

        if (count($dialogMessages)) {

            // get id of the user we are talking to
            $anotherUserId = ($dialogMessages[0]->send == $currentUserId) ?
                $dialogMessages[0]->recive :
                $dialogMessages[0]->send;

            $anotherUserObj = User::where('id', $anotherUserId)->first();
            $this->checkAvatarExist($anotherUserObj);

            foreach($dialogMessages as $dialog) {

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
        }
        else {
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
     * @param $obj
     * @param $currentDate string
     * @return void
     */
    private function formatChatDate($obj, $currentDate = '') {
        $chatDate = Carbon::parse($obj->created_at);

        if(empty($currentDate)) {
            $currentDate = Carbon::now()->format('d.m.Y');
        }

        if($currentDate == $chatDate->format('d.m.Y')) {
            $obj->created_at =  $chatDate->format('H:i');
        } else {
            $obj->created_at = $chatDate->format('d.m.Y H:i');
        }
    }

    /**
     * If empty avatar then set No avatar IMG
     * @param $obj
     * @return void
     */
    private function checkAvatarExist($obj) {

        if(!$obj->avatar)
            $obj->avatar = ProfileEnum::NO_AVATAR;
    }
}