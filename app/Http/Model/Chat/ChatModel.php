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

        $userChats = DB::table('users')
            ->select('messages.text', 'users.name', 'users.id','users.avatar',
                'dialog.dialog_id', 'messages.created_at')
            ->leftJoin('messages', 'users.id', '=', 'messages.recive')
            ->leftJoin('dialog', 'messages.dialog', '=', 'dialog.dialog_id')
            ->where(function($query)
            {
                $query->where('messages.send', Auth::user()->id)
                    ->orWhere('messages.recive', Auth::user()->id);
            })
            ->orderBy('messages.updated_at', 'DESC')
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('users.name', 'ASC')
            ->groupBy('users.id')
            ->get();

        foreach($userChats as $k => $chat) {

            self::formatChatDate($chat);
            if(strlen($chat->text) >= 50)
                $chat->text =  \Illuminate\Support\Str::limit($chat->text, 50);

            self::checkAvatarExist($chat);
        }

        return $userChats;
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

    protected static function sendMessage(string $message, int $dialogId, int $userId) {

        $dialogId = self::dialog($userId, $dialogId, $message);
        return $dialogId;
    }

    /**
     * Get dialog Id
     * @param $userId int
     * @param $dialogId int
     * @return int
     */
    protected static function getDialogId($userId, $dialogId = 0): int {

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

        if(empty($dialogExist))
        {
            $dialogId = DB::table('dialog')->insertGetId(
            [
                'send' =>  Auth::user()->id,
                'recive' => $userId
            ]);
        }
        else {
            $dialogId = $dialogExist->dialog_id;
        }

        return $dialogId;
    }

    protected static function dialog($userId, $dialogId, $message = '') {
        $getDialogId = self::getDialogId($userId, $dialogId);
        $messageId = self::insertMessage($userId, $getDialogId, $message);

        return $messageId;
    }

    /**
     * Insert message into dialog
     * @param $userId int
     * @param $dialogId int
     * @param $message string
     * @return int
     */
    private static function insertMessage($userId, $dialogId, $message) {

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

    public static function getUserDialog(int $userId) {

        if (!User::where('id', $userId)->exists())
            return ['error' => 'user not exist'];

        $dialogId = self::getDialogId($userId);

        $currentUserId =  Auth::user()->id;
        $anotherUserObj = User::where('id', $userId)->first();

        self::checkAvatarExist($anotherUserObj);

        $dialogMessages = DB::table('messages')
        ->select( 'messages.text', 'messages.dialog', 'messages.created_at',
                  'messages.updated_at', 'messages.send', 'messages.recive',
                  'messages.text' )
            ->where('dialog', $dialogId)
        ->orderBy('updated_at', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();

        $currentUserAvatar = Auth::user()->avatar ?: static::$noAvatarPath;

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

        return ['dialogMessages' => $dialogMessages, 'dialogId' => $dialogId];
    }
}
