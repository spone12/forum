<?php

namespace App\Repository\Chat\Dialog;

use App\Contracts\Chat\Dialog\DialogCommandRepositoryInterface;
use App\Enums\Chat\{ChatRole, DialogType};
use App\Models\Chat\DialogModel;
use Illuminate\Support\Facades\DB;

/**
 * Class DialogCommandRepository
 *
 * @package App\Repository\Chat\Dialog
 */
class DialogCommandRepository implements DialogCommandRepositoryInterface
{
    /**
     * Create a dialogue between users
     *
     * @param int        $userId
     * @param int        $anotherUserId
     * @param DialogType $dialogType
     *
     * @return int
     */
    public function createDialogBetweenUsers(int $userId, int $anotherUserId, DialogType $dialogType): int
    {
        return DB::transaction(function () use ($userId, $anotherUserId, $dialogType) {
            $dialog = DialogModel::create([
                'created_by' => $userId,
                'type' => $dialogType
            ]);

            $dialog->participants()->createMany([
                ['user_id' => $userId, 'role' => ChatRole::OWNER],
                ['user_id' => $anotherUserId, 'role' => ChatRole::MEMBER],
            ]);

            return $dialog->dialog_id;
        });
    }
}
