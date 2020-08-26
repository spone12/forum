<?php

namespace App\Http\Model\Notation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class NotationViewModel extends Model
{
    protected $table = 'views_notation';

    /*protected $fillable = [
        'id_user', 'name_notation', 'text_notation','notation_add_date'
    ];*/

    public $timestamps = false;

    

   
}
