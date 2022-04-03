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

        $date = date('d.m.Y');
        foreach($userChats as $chat){
            self::formatChatDate($chat, $date);

            if(strlen($chat->text) >= 50)
                $chat->text =  \Illuminate\Support\Str::limit($chat->text, 50);

            self::checkAvatarExist($chat);
        }

        return $userChats;
    }

    private static function formatChatDate($obj, $date) {
        $chatDate = date('d.m.Y', strtotime($obj->created_at));

        if($date == $chatDate){
            $obj->created_at =  date('H:i', strtotime($obj->created_at));
        }else{
            $obj->created_at =  date('d.m.Y H:i', strtotime($obj->created_at));
        }
    }

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

            $userObj = User::where('id', $search->send)->get();

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

    protected static function getDialog($userId, $dialogId = 0) {

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
        $getDialogId = self::getDialog($userId, $dialogId);
        $messageId = self::insertMessage($userId, $getDialogId, $message); 
        
        return $messageId;
    }

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

        if(!User::where('id', $userId)->exists())
            return ['error' => 'user not exist'];

        $dialogId = self::getDialog($userId);

        $currentUserId =  Auth::user()->id;
        $anotherUserObj = User::where('id', $userId)->get();

        self::checkAvatarExist($anotherUserObj[0]);

        $dialogMessages = DB::table('messages')
        ->select( 'messages.text', 'messages.dialog', 'messages.created_at', 
                  'messages.updated_at', 'messages.send', 'messages.recive',
                  'messages.text' )
            ->where('dialog', $dialogId)
        ->orderBy('updated_at', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();

        $date = date('d.m.Y');
        $currentUserAvatar = Auth::user()->avatar ?: static::$noAvatarPath;;

        foreach($dialogMessages as $dialog){

            $dialog->difference = 
                Carbon::createFromFormat('Y-m-d H:i:s', $dialog->created_at)->diffForHumans();

            self::formatChatDate($dialog, $date);

            if($dialog->send == $currentUserId){
                $dialog->name = Auth::user()->name;
                $dialog->avatar = $currentUserAvatar;
                $dialog->id = $currentUserId;
            }else{
                $dialog->name = $anotherUserObj[0]->name;
                $dialog->avatar = $anotherUserObj[0]->avatar;
                $dialog->id = $anotherUserObj[0]->id;
            }
        }
        
        return [$dialogMessages, $dialogId];
    }
}
