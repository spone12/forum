<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ChatModel
 * @package App\Models\Chat
 */
class ChatModel extends Model
{
    /** @var string */
    protected $table = 'messages';
    /** @var string */
    protected $primaryKey = 'message_id';
    /** @var string[] */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dialogObject()
    {
        return $this->hasOne('\App\Models\Chat\DialogModel', 'dialog_id', 'dialog');
    }
}
