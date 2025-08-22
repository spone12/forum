<?php

namespace App\Models\Chat;

use App\Enums\Chat\ChatRole;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DialogParticipant
 *
 * @package App\Models\Chat
 */
class DialogParticipant extends Model
{
    /** @var string */
    protected $table = 'dialog_participants';

    /** @var string  */
    protected $primaryKey = 'id';

    /** @var string[] */
    protected $fillable = ['dialog_id', 'user_id', 'role'];

    /** @var \class-string[] */
    protected $casts = [
        'role' => ChatRole::class,
    ];

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
}
