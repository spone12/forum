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
     * Get user dialog
     *
     * @param int $userId
     * @return
     */
    public function getUserDialog(int $userId, DialogType $dialogType)
    {
        return DialogModel::query()
            ->select('dialog_id')
            ->where('type', $dialogType)
            ->whereHas('participants', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();
    }
}
