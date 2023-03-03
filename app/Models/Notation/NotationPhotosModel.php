<?php

namespace App\Models\Notation;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotationPhotosModel
 *
 * @property int  $notation_photo_id
 * @property int  $id_user
 * @property int  $notation_id
 * @property string $path_photo
* @property date $photo_add_date
* @property date $photo_edit_date
 *
 * @package App\Models\Notation
 */
class NotationPhotosModel extends Model
{
    /** @var string */
    protected $table = 'notation_photos';
    /** @var string */
    protected $primaryKey = 'notation_photo_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notation() {
        return $this->belongsTo(NotationModel::class, 'notation_id', 'notation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'id', 'id_user');
    }
}
