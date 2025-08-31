<?php

namespace App\Models\Chat;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Class MessagesModel
 *
 * @property int            $message_id
 * @property int            $dialog_id
 * @property int            $user_id
 * @property string         $text
 * @property bool           $read
 * @property timestamp|null $created_at
 * @property timestamp|null $updated_at
 * @property timestamp|null $deleted_at
 *
 * @package App\Models\Chat
 */
class MessagesModel extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'messages';

    /**
     * @var string
     */
    protected $primaryKey = 'message_id';

    /**
     * @var string[]
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'dialog_id',
        'text'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        //'created_at' => 'date:H:i',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['difference', 'created_at_hour'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dialog()
    {
        return $this->belongsTo(DialogModel::class, 'dialog_id', 'dialog_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the time difference between the date the message was created and the current time
     *
     * @return string
     */
    public function getDifferenceAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Abbreviated time
     *
     * @return string
     */
    public function getCreatedAtHourAttribute()
    {
        return Carbon::parse($this->created_at)->format('H:i');
    }

    /**
     * @return Attribute
     */
    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::get(function () {
            $createdAt = $this->created_at;

            return $createdAt->isToday()
                ? $createdAt->format('H:i')
                : $createdAt->format('d.m.Y H:i');
        });
    }
}
