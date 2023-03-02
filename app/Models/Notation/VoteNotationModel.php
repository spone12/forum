<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Notation\NotationModel;
use App\User;

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
