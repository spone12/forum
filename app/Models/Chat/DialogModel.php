<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DialogModel
 *
 * @property int $dialog_id
 * @property int $send
 * @property int $recive
 * @property timestamp $date_create
 *
 * @package App\Models\Chat
 */
class DialogModel extends Model
{
    /** @var string */
    protected $table = 'dialog';
    /** @var string */
    protected $primaryKey = 'dialog_id';
    /** @var string[] */
    protected $dates = ['date_create'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages ()
    {
        return $this->hasMany(ChatModel::class, 'dialog', 'dialog_id');
    }
}
