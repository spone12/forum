<?php
namespace App\Contracts\Chat\Dialog;

use App\Enums\Chat\DialogType;

/**
 * Interface DialogCommandRepositoryInterface
 *
 * @package App\Contracts\Chat\Dialog
 */
interface DialogCommandRepositoryInterface
{
    public function createDialogWithParticipants(int $userId, DialogType $dialogType);
}
