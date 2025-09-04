<?php

namespace App\Service\Chat\Dialog;

use AllowDynamicProperties;
use App\Contracts\Chat\Dialog\{
    DialogCommandRepositoryInterface
};
use App\Enums\Chat\DialogType;

/**
 * DialogCommandService class
 *
 * @package
 */
#[AllowDynamicProperties]
class DialogCommandService
{
    protected DialogCommandRepositoryInterface $dialogCommandRepository;

    function __construct(
        DialogCommandRepositoryInterface $dialogCommandRepository
    ) {
        $this->dialogCommandRepository = $dialogCommandRepository;
    }

    /**
     * Create dialog between users service
     *
     * @param int $userId
     * @param int $anotherUserId
     * @param DialogType $dialogType
     * @return mixed
     */
    public function createDialogBetweenUsers(
        int $userId,
        int $anotherUserId,
        DialogType $dialogType = DialogType::PRIVATE
    ) {
        return $this->dialogCommandRepository->createDialogBetweenUsers($userId, $anotherUserId, $dialogType);
    }
}
