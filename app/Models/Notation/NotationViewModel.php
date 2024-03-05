<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class NotationViewModel
 *
 * @property int  $notation_views_id
 * @property int  $notation_id
 * @property int  $counter_views
 * @property date $view_date
 *
 * @package App\Models\Notation
 */
class NotationViewModel extends Model
{
    /** @var string */
    protected $table = 'notation_views';
    /** @var string */
    protected $primaryKey = 'notation_views_id';
    /** @var bool */
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'notation_id', 'counter_views', 'view_date'
    ];

    /**
     * Create or increment view count
     *
     * @param int $notationId
     */
    protected static function addViewNotation(int $notationId)
    {

        $today = Carbon::today();
        $notationView = NotationViewModel::firstOrNew([
            'notation_id' => $notationId,
            'view_date' => $today->toDateString()
        ]);
        $notationView->counter_views++;
        $notationView->save();
    }
}
