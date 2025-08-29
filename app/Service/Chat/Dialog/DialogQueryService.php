<?php

namespace App\Service\Chat\Dialog;

use AllowDynamicProperties;
use App\Contracts\Chat\Dialog\{
    DialogQueryRepositoryInterface
};
use App\Enums\Cache\CacheKey;
use App\Enums\Chat\DialogType;
use Illuminate\Support\Collection;

/**
 * DialogQueryService class
 *
 * @package
 */
#[AllowDynamicProperties]
class DialogQueryService
{
    protected DialogQueryRepositoryInterface $dialogQueryRepository;

    function __construct(
        DialogQueryRepositoryInterface   $dialogQueryRepository,
    ) {
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
     * Get private dialog
     *
     * @param int $userId
     * @param int $anotherUserId
     * @return mixed
     */
    public function getPrivateDialog(int $userId, int $anotherUserId) {
        return $this->dialogQueryRepository->getDialog($userId, $anotherUserId, DialogType::PRIVATE);
    }
}
