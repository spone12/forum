<?php

namespace App\Observers;

use App\Models\Notation\NotationModel;

class NotationsObserver
{
    /**
     * Handle the NotationModel "created" event.
     *
     * @param  \App\Models\Notation\NotationModel  $appModelsNotation
     * @return void
     */
    public function created(NotationModel $appModelsNotation)
    {
        //
    }

    /**
     * Handle the NotationModel "updated" event.
     *
     * @param  \App\Models\Notation\NotationModel  $appModelsNotation
     * @return void
     */
    public function updated(NotationModel $appModelsNotation)
    {
        //
    }

    /**
     * Handle the NotationModel "deleted" event.
     *
     * @param  \App\Models\Notation\NotationModel  $appModelsNotation
     * @return void
     */
    public function deleted(NotationModel $appModelsNotation)
    {
        //
    }

    /**
     * Handle the NotationModel "restored" event.
     *
     * @param  \App\Models\Notation\NotationModel  $appModelsNotation
     * @return void
     */
    public function restored(NotationModel $appModelsNotation)
    {
        //
    }

    /**
     * Handle the NotationModel "force deleted" event.
     *
     * @param  \App\Models\Notation\NotationModel  $appModelsNotation
     * @return void
     */
    public function forceDeleted(NotationModel $appModelsNotation)
    {
        //
    }
}
