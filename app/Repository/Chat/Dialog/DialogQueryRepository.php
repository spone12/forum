<?php

namespace App\Repository\Chat\Dialog;

use App\Contracts\Chat\Dialog\DialogQueryRepositoryInterface;
use App\Enums\Chat\DialogType;
use App\Models\Chat\DialogModel;

/**
 * Class DialogQueryRepository
 *
 * @package App\Repository\Chat\Dialog
 */
class DialogQueryRepository implements DialogQueryRepositoryInterface
{
    /**
     * Finding a dialogue between participants
     *
     * @param int $userId
     * @param int $anotherUserId
     * @param DialogType $dialogType
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getDialog(int $userId, int $anotherUserId, DialogType $dialogType)
    {
        return DialogModel::query()
            ->select('dialog_id')
            ->where('type', $dialogType)
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('participants', function ($q) use ($anotherUserId) {
                $q->where('user_id', $anotherUserId);
            })
            ->first();
    }
}
