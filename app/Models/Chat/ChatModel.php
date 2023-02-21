<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class ChatModel extends Model
{
    protected $table = 'messages';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $primaryKey = 'message_id';

    public function dialogObject() {
        return $this->hasOne('\App\Models\Chat\DialogModel', 'dialog_id', 'dialog');
    }
}
