<?php

namespace App\Models\Notation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'notation_views';
    /**
     * @var string
     */
    protected $primaryKey = 'notation_views_id';
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'notation_id', 'counter_views', 'view_date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notations()
    {
        return $this->belongsTo(NotationModel::class, 'notation_id', 'notation_id');
    }

    /**
     * Create or increment view count
     *
     * @param int $notationId
     */
    protected static function addViewNotation(int $notationId)
    {
        $notationView = NotationViewModel::firstOrNew([
            'notation_id' => $notationId,
            'view_date' => Carbon::today()->toDateString()
        ]);
        $notationView->counter_views++;
        $notationView->save();
    }
}
