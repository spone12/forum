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

    private function insertMessage()
    {   
        /*$dialogId = DB::table('dialog')->insertGetId(
            [
                'send' =>  Auth::user()->id,
                'recive' => $userId
            ]);

        return $dialogId;*/

        $id = DB::table('messages')->insertGetId(
            ['text' => 'john@example.com', 'votes' => 0]
          );
    }

    protected static function dialog(int $userId)
    {
        if(DialogModel::where('send', $userId)->orWhere('recive', $userId)->exists())
        {
            return $userId;
        }
    }
}
