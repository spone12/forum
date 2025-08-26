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
     * Create a dialogue and dialogue participants
     *
     * @param int        $userId
     * @param DialogType $dialogType
     *
     * @return int
     */
    public function createDialogWithParticipants(int $userId, DialogType $dialogType): int
    {
        return DB::transaction(function () use ($userId, $dialogType) {
            $dialog = DialogModel::create([
                'created_by' => auth()->id(),
                'type' => $dialogType
            ]);

            $dialog->participants()->createMany([
                ['user_id' => auth()->id(), 'role' => ChatRole::OWNER],
                ['user_id' => $userId, 'role' => ChatRole::MEMBER],
            ]);

            return $dialog->dialog_id;
        });
    }
}
