<?php

namespace App\Service\Chat\Dialog;

use AllowDynamicProperties;
use App\Contracts\Chat\Dialog\{
    DialogCommandRepositoryInterface,
    DialogQueryRepositoryInterface
};
use App\Enums\Cache\CacheKey;
use App\Enums\Chat\DialogType;
use App\Models\Chat\DialogModel;
use Illuminate\Support\Collection;

/**
 * DialogService class
 *
 * @package
 */
#[AllowDynamicProperties]
class DialogService
{
    protected DialogCommandRepositoryInterface $dialogCommandRepository;
    protected DialogQueryRepositoryInterface $dialogQueryRepository;

    function __construct(
        DialogCommandRepositoryInterface $dialogCommandRepository,
        DialogQueryRepositoryInterface   $dialogQueryRepository,
    ) {
        $this->dialogCommandRepository = $dialogCommandRepository;
        $this->dialogQueryRepository = $dialogQueryRepository;
    }

    /**
     * Dialog chat list service
     *
     * @param int $limit
     * @return Collection
     */
    public function dialogList(int $limit = 0):Collection
    {
        $userDialogs = auth()->user()
            ->dialogs()
            ->with([
                'participants.user',
                'lastMessage.user'
            ])
            ->whereHas('messages')
            ->get();

        if ($limit) {
            $userDialogs = $userDialogs->take($limit);
        }

        foreach ($userDialogs as $dialog) {
            $lastMessage = $dialog->lastMessage;

            $dialog->id = $lastMessage->user_id;
            $dialog->name = $lastMessage->user->name;
            $dialog->avatar = $lastMessage->user->avatar;
            $dialog->created_at = $lastMessage->created_at;
            $dialog->isRead = $lastMessage->read;
            $dialog->isOnline = \Cache::get(CacheKey::USER_IS_ONLINE->value . $lastMessage->user_id);
            $dialog->difference = $lastMessage->created_at->diffForHumans();
            $dialog->text = \Str::limit($lastMessage->text, 50);
        }
        return $userDialogs->sortByDesc('created_at');
    }

    /**
     * Get dialog Id or create new
     *
     * @param int        $userId
     * @param int        $dialogId
     * @param DialogType $dialogType
     *
     * @return int
     */
    public function getDialogId(
        int $userId,
        int $dialogId = 0,
        DialogType $dialogType = DialogType::PRIVATE
    ): int {

        if ($dialogId === 0) {
            $dialog = $this->dialogQueryRepository->getUserDialog($userId, $dialogType);
        } else {
            $dialog = DialogModel::where('dialog_id', $dialogId)->firstOrFail();
        }

        if (empty($dialog)) {
            $dialogId = $this->dialogCommandRepository->createDialogWithParticipants($userId, $dialogType);
        } else {
            $dialogId = $dialog->dialog_id;
        }

        return $dialogId;
    }
}
