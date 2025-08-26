<?php
namespace App\Contracts\Chat\Dialog;

use App\Enums\Chat\DialogType;

/**
 * Interface DialogQueryRepositoryInterface
 *
 * @package App\Contracts\Chat\Dialog
 */
interface DialogQueryRepositoryInterface
{
    public function getUserDialog(int $userId, DialogType $dialogType);
}
