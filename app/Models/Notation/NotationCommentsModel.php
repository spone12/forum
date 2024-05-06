<?php

namespace App\Models\Notation;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotationCommentsModel
 *
 * @property int  $comment_id
 * @property int  $user_id
 * @property int  $notation_id
 * @property string $text
 * @property date $created_at
 * @property date $updated_at
 * @property date $deleted_at
 *
 * @package App\Models\Notation
 */
class NotationCommentsModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'notation_comments';
    /**
     * @var string
     */
    protected $primaryKey = 'comment_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notations()
    {
        return $this->belongsTo(NotationModel::class, 'notation_id', 'notation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
