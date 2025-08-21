<?php

namespace App\Models\Chat;

use App\Enums\Chat\DialogType;
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
    /**
     * @var string
     */
    protected $table = 'dialog';
    /**
     * @var string
     */
    protected $primaryKey = 'dialog_id';
    /**
     * @var string[]
     */
    protected $dates = ['date_create'];

    /** @var \class-string[] */
    protected $casts = [
        'type' => DialogType::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(MessagesModel::class, 'dialog', 'dialog_id');
    }
}
