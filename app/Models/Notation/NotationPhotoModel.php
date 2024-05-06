<?php

namespace App\Models\Notation;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotationPhotosModel
 *
 * @property int  $notation_photo_id
 * @property int  $user_id
 * @property int  $notation_id
 * @property string $path_photo
 * @property date $photo_add_date
 * @property date $photo_edit_date
 *
 * @package App\Models\Notation
 */
class NotationPhotoModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'notation_photo';
    /**
     * @var string
     */
    protected $primaryKey = 'notation_photo_id';

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
