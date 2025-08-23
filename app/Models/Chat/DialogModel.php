<?php

namespace App\Models\Chat;

use App\Enums\Chat\DialogType;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DialogModel
 *
 * @property int    $dialog_id
 * @property string $title
 * @property enum   $type
 * @property int    $created_by
 * @property int    $send
 * @property int    $recive
 * @property string $date_create
 *
 * @package App\Models\Chat
 */
class DialogModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'dialogs';
    /**
     * @var string
     */
    protected $primaryKey = 'dialog_id';
    /**
     * @var string[]
     */
    protected $dates = ['date_create'];

    /** @var bool */
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'title',
        'type',
        'created_by',
        'send',
        'recive'
    ];

    /** @var \class-string[] */
    protected $casts = [
        'type' => DialogType::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(DialogParticipants::class, 'dialog_id', 'dialog_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
