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

    protected $chat;

    function __construct()
    {
       
    }
    protected static function searchChat(String $word)
    {   
        $search = DB::table('users')
        ->select('messages.text', 'users.name', 'users.id','users.avatar', 'dialog.dialog_id')
            ->leftJoin('messages', 'users.id', '=', 'messages.recive')
            ->leftJoin('dialog', 'messages.dialog', '=', 'dialog.dialog_id') 
        ->where(function($query)
        {
            $query->where('users.id',  Auth::user()->id)
                  ->orWhere('users.id', 'messages.recive')
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

        //print_r($search);

        return $search;
    }

    protected static function sendMessage(string $message, int $dialogId, int $userId)
    {
        $dialogId = self::dialog($userId, $dialogId, $message);

        return $dialogId;
    }

    protected static function dialog($userId, $dialogId, $message)
    {
        if(!$dialogId)
        {
            $dialogId = 0;

            $dialogSendExist = DB::table('dialog AS d')
            ->select('d.dialog_id')
                ->where('d.send',  Auth::user()->id)
                ->where('d.recive', $userId)
            ->first();
            
            $dialogReciveExist = DB::table('dialog AS d')
            ->select('d.dialog_id')
                ->where('d.send',  $userId)
                ->where('d.recive', Auth::user()->id)
            ->first();

            if(empty($dialogSendExist) || empty($dialogReciveExist))
            {
                $dialogId = DB::table('dialog')->insertGetId(
                [
                    'send' =>  Auth::user()->id,
                    'recive' => $userId
                ]);
            }
            else {
                $dialogId = !empty($dialogSendExist) ?$dialogSendExist->dialog_id:$dialogReciveExist->dialog_id;
            }
        }

        $messageId = self::insertMessage($userId, $dialogId, $message); 
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
}
