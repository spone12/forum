<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class NotationViewModel
 * @package App\Models\Notation
 */
class NotationViewModel extends Model
{
    /** @var string */
    protected $table = 'views_notation';
    /** @var string */
    protected $primaryKey = 'views_notation_id';
    /** @var bool */
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'notation_id', 'counter_views', 'view_date'
    ];

    /**
     * @param int $notationId
     */
    protected static function addViewNotation(int $notationId) {

       $check_note = NotationViewModel::where('view_date', '=', Carbon::now()->format('Y-m-d'))
            ->where('notation_id', '=', $notationId)
        ->exists();

        if ($check_note) {

            NotationViewModel::where('view_date', '=', Carbon::now()->format('Y-m-d'))
                ->where('notation_id', '=', $notationId)
            ->increment('counter_views');
        } else {

            $add = new NotationViewModel();
                $add->notation_id = $notationId;
                $add->counter_views  = $add->counter_views + 1;
                $add->view_date =  Carbon::now()->format('Y-m-d');
            $add->save();
        }
    }
}
