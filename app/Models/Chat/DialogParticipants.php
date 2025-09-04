<?php

namespace App\Models\Chat;

use App\Enums\Chat\ChatRole;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DialogParticipants
 *
 * @property int    $id
 * @property int    $dialog_id
 * @property int    $user_id
 * @property string $role
 * @property string $joined_at
 *
 * @package App\Models\Chat
 */
class DialogParticipants extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'dialog_participants';

    /** @var string  */
    protected $primaryKey = 'id';

    /** @var string[] */
    protected $fillable = [
        'dialog_id',
        'user_id',
        'role'
    ];

    /** @var bool */
    public $timestamps = false;

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
