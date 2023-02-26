<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use App\Models\NotationModel;
use App\User;

class VoteNotationModel extends Model
{
    protected $table = 'vote_notation';
    protected $primaryKey = 'vote_notation_id';
    public $timestamps = false;

    public function notation() {
        return $this->belongsTo(NotationModel::class, 'notation_id', 'notation_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'id', 'id_user');
    }
}
