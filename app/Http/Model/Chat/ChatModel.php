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

    protected static function getUserChats(){

        $userChats = DB::table('users')
        ->select('messages.text', 'users.name', 'users.id','users.avatar', 'dialog.dialog_id', 'messages.created_at')
            ->leftJoin('messages', 'users.id', '=', 'messages.recive')
            ->leftJoin('dialog', 'messages.dialog', '=', 'dialog.dialog_id') 
        ->where(function($query)
        {
            $query->where('messages.send', Auth::user()->id)
              ->orWhere('messages.recive', Auth::user()->id);
        })
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('users.name', 'ASC')
        ->groupBy('users.id')
        ->get();

        $date = date('d.m.Y');
        foreach($userChats as $chat){
            $chatDate = date('d.m.Y', strtotime($chat->created_at));

            if($date == $chatDate){
                $chat->created_at =  date('H:i', strtotime($chat->created_at));
            }else{
                $chat->created_at =  date('d.m.Y H:i', strtotime($chat->created_at));
            }

            if(strlen($chat->text) >= 50)
                $chat->text =  \Illuminate\Support\Str::limit($chat->text, 50);

            if(!$chat->avatar)
                $chat->avatar =  'img/avatar/no_avatar.png';
        }
 
        return $userChats;
    }

    protected static function searchChat(String $word){   
        $search = DB::table('users')
        ->select('messages.text', 'users.name', 'users.id','users.avatar', 'dialog.dialog_id')
            ->leftJoin('messages', 'users.id', '=', 'messages.recive')
            ->leftJoin('dialog', 'messages.dialog', '=', 'dialog.dialog_id') 
        ->where('users.id',  Auth::user()->id)
        ->where(function($query)
        {
            $query->where('users.id', 'messages.recive')
                  ->orWhere('users.id', 'messages.send');
        })
        ->where(function($query) use (&$word)
        {
            $query->where('users.name', 'like', '%'.$word.'%')
                  ->orWhere('messages.text', 'like', '%' . $word . '%');
        })
        //->groupBy('users.id')
        ->orderBy('messages.created_at', 'DESC')
        ->orderBy('users.name', 'ASC')
        ->limit(10)
        ->get();

        echo $word;
        print_r($search);
        return $search;
    }

    protected static function sendMessage(string $message, int $dialogId, int $userId)
    {
        $dialogId = self::dialog($userId, $dialogId, $message);

        return $dialogId;
    }

    protected static function getDialog($userId, $dialogId = 0)
    {
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

    protected static function dialog($userId, $dialogId, $message = '')
    {
        $getDialogId = self::getDialog($userId, $dialogId);
        $messageId = self::insertMessage($userId, $getDialogId, $message); 
        
        return $messageId;
    }

    private static function insertMessage($userId, $dialogId, $message)
    {   
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

    public static function getuserDialog(int $userId){

        $userExist = User::where('id', $userId)->exists();

        if(!$userExist)
            return ['error' => 'user not exist'];

        $dialogId = self::getDialog($userId);

        $dialogGet = DB::table('dialog as d')
        ->select('messages.text', 'u.name', 'u.id','u.avatar', 'd.dialog_id')
            ->join('users AS u', 'd.send', '=', 'u.id') 
            ->leftJoin('messages', 'u.id', '=', 'messages.recive')
            ->where('d.dialog_id', $dialogId)
        ->get();
        
        echo "<pre>".print_r($dialogGet)."</pre>";
    }
}
