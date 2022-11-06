<?php

namespace App\Http\Model\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Http\Model\Chat\DialogModel as DialogModel;

class ChatModel extends Model
{
    protected $table = 'messages';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $primaryKey = 'message_id';
    public static $noAvatarPath = 'img/avatar/no_avatar.png';

    /**
     * Get current user's dialogs
     * @return array
     */
    protected static function getUserChats() {

        $userDialogs = DB::table('dialog')
            ->select('dialog_id', 'send', 'recive')
            ->where(function($query)
            {
                $query->where('dialog.send', Auth::user()->id)
                    ->orWhere('dialog.recive', Auth::user()->id);
            })
        ->get();

        foreach($userDialogs as $k => $chat) {

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

            self::checkAvatarExist($userDialogs[$k]);

            if(strlen($lastMessage->text) >= 50)
                $lastMessage->text =  \Illuminate\Support\Str::limit($chat->text, 50);
        }

        return $userDialogs;
    }

    /**
     * Formate create date message value
     * @param $obj
     * @param $currentDate string
     * @return void
     */
    private static function formatChatDate($obj, $currentDate = '') {
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
    private static function checkAvatarExist($obj) {

        if(!$obj->avatar)
            $obj->avatar = static::$noAvatarPath;
    }

    protected static function searchChat(String $word) {

        $searchResult = DB::table('dialog')
        ->select(  'messages.send','dialog.dialog_id', 'messages.created_at','messages.text')
            ->join('users', 'dialog.recive', '=', 'users.id')
            ->join('users as user2', 'dialog.send', '=', 'user2.id')
            ->leftJoin('messages','messages.dialog', '=', 'dialog.dialog_id' )
        ->where(function($query)
        {
            $query->where('messages.recive', Auth::user()->id)
                  ->orWhere('messages.send', Auth::user()->id);
        })
        ->where(function($query) use (&$word)
        {
            $query->where('messages.text', 'like', '%' . $word . '%')
                    ->orWhere('users.name', 'like', '%'.$word.'%')
                    ->orWhere('user2.name', 'like', '%'.$word.'%');
        })
        //->groupBy('users.id')
        ->orderBy('messages.created_at', 'DESC')
        ->orderBy('users.name', 'ASC')
        ->orderBy('user2.name', 'ASC')
        ->limit(10)
        ->get();

        foreach($searchResult as $search){

            $userObj = User::where('users.name')->get();

            $search->avatar = $userObj[0]->avatar ?: static::$noAvatarPath;;
            $search->id = $userObj[0]->id;
            $search->name = $userObj[0]->name;
        }

        return $searchResult;
    }

    /**
     * Send message in dialog
     * @param $message string
     * @param $dialogId int
     * @param $userId int
     * @return int
     */
    protected static function sendMessage(string $message, int $dialogId, int $userId) {

        $dialogId = ChatModel::getDialogId($userId, $dialogId);
        $messageId = DB::table('messages')->insertGetId(
            [
                'dialog' => $dialogId,
                'send' =>  Auth::user()->id,
                'recive' => $userId,
                'text' => $message,
                'created_at' => Carbon::now()
            ]);

        return $messageId;
    }

    /**
     * Get dialog Id
     * @param $userId int
     * @param $dialogId int
     * @return int
     */
    protected static function getDialogId($userId, $dialogId = 0): int {

        if($dialogId == 0) {
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

        if(empty($dialogExist) || $dialogExist == false)
        {
            $dialogId = DB::table('dialog')->insertGetId(
            [
                'send' =>  Auth::user()->id,
                'recive' => $userId
            ]);
        }
/*        else {
            $dialogId = DialogModel::where('dialog_id', $dialogId)->dialog_id;
        }*/

        return $dialogId;
    }

    /**
     * Insert message into dialog
     * @param $userId int
     * @param $dialogId int
     * @param $message string
     * @return int
     */
    public static function getUserDialog(int $dialogId, $userMessageWithId = 0) {

        if (!DialogModel::where('dialog_id', $dialogId)->exists()) {
            return ['error' => 'Chat not exist'];
        }

        $currentUserId =  Auth::user()->id;
        $currentUserAvatar = Auth::user()->avatar ?: static::$noAvatarPath;

        $dialogMessages = DB::table('messages')
        ->select( 'messages.text', 'messages.dialog', 'messages.created_at',
                  'messages.updated_at', 'messages.send', 'messages.recive',
                  'messages.text' )
            ->where('dialog', $dialogId)
        ->orderBy('created_at', 'asc')
        ->get();

        if(count($dialogMessages)) {
            // get the id of the user we are talking to
            $anotherUserId = ($dialogMessages[0]->send == $currentUserId) ?
                $dialogMessages[0]->recive :
                $dialogMessages[0]->send;

            $anotherUserObj = User::where('id', $anotherUserId)->first();
            self::checkAvatarExist($anotherUserObj);

            foreach($dialogMessages as $dialog) {

                $dialog->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $dialog->created_at)->diffForHumans();

                self::formatChatDate($dialog);

                if($dialog->send == $currentUserId) {
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

        return ['dialogMessages' => $dialogMessages, 'dialogId' => $dialogId, 'recive' => $anotherUserId ];
    }
}
