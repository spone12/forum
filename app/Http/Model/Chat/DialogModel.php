<?php

namespace App\Http\Model\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DialogModel extends Model
{
    protected $table = 'dialog';
    protected $dates = ['date_create'];
    protected $primaryKey = 'dialog_id';
}
