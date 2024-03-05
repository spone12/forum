<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class VoteNotationModel
 *
 * @property int $vote_notation_id
 * @property int $id_user
 * @property int $notation_id
 * @property smallint $vote
 * @property double $start
 * @property timestamp|null $vote_date
 *
 * @package App\Models\Notation
 */
class VoteNotationModel extends Model
{
    /** @var string */
    protected $table = 'vote_notation';
    /** @var string */
    protected $primaryKey = 'vote_notation_id';
    /** @var bool */
    public $timestamps = false;

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
