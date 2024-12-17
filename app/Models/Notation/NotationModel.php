<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class NotationModel
 *
 * @property int $notation_id
 * @property int $user_id
 * @property smallint $category
 * @property string $name_notation
 * @property string $text_notation
 * @property int $rating
 * @property double $star_rating
 * @property timestamp $notation_add_date
 * @property timestamp|null $notation_edit_date
 *
 * @package App\Models\Notation
 */
class NotationModel extends Model
{
    use hasFactory;

    /**
     * @var string
     */
    protected $table = 'notations';

    /**
     * @var string
     */
    protected $primaryKey = 'notation_id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'name_notation',
        'text_notation',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function notationViews()
    {
        return $this->hasMany(NotationViewModel::class, 'notation_id', 'notation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notationsVote()
    {
        return $this->hasMany(VoteNotationModel::class, 'notation_id', 'notation_id');
    }

     /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notationPhoto()
    {
        return $this->hasMany(NotationPhotoModel::class, 'notation_id', 'notation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notationComments()
    {
        return $this->hasMany(NotationCommentsModel::class, 'notation_id', 'notation_id');
    }
}
