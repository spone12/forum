<?php

namespace App\Http\Model\Notation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class NotationViewModel extends Model
{
    protected $table = 'views_notation';

    protected $fillable = [
        'notation_id', 'counter_views', 'view_date'
    ];
    protected $primaryKey = 'views_notation_id';

    public $timestamps = false;

    protected static function add_view_notation(int $notation_id)
    {
       $check_note = NotationViewModel::where('view_date', '=', Carbon::now()->format('Y-m-d'))
                                        ->where('notation_id', '=', $notation_id)
                                        ->exists();

        if($check_note)
        {
            NotationViewModel::where('view_date', '=', Carbon::now()->format('Y-m-d'))
                                ->where('notation_id', '=', $notation_id)
                            ->increment('counter_views');
        }
        else 
        {
            $add = new NotationViewModel();

                $add->notation_id = $notation_id;
                $add->counter_views  = $add->counter_views + 1;
                $add->view_date =  Carbon::now()->format('Y-m-d');
            $add->save();
        }
           
    }

   
}
