<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MessagesModel
 *
 * @property int $message_id
 * @property int $dialog
 * @property int $send
 * @property int $recive
 * @property text $text
 * @property tinyint $read
 * @property timestamp|null $created_at
 * @property timestamp|null $updated_at
 * @property timestamp|null $deleted_at
 *
 * @package App\Models\Chat
 */
class MessagesModel extends Model
{
    use SoftDeletes;

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
        return $this->hasOne(DialogModel::class, 'dialog_id', 'dialog');
    }
}
