<?php

namespace App\Service\Chat\Notifications;

use App\Enums\Cache\CacheKey;
use App\Repository\Chat\Notifications\MessageNotificationsRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Class MessageNotificationsService
 * @package App\Service
 */
class MessageNotificationsService
{
    /** @var MessageNotificationsRepository */
    protected $repository;

    /**
     * MessageNotificationsService constructor.
     *
     * @param MessageNotificationsRepository $repository
     */
    function __construct(MessageNotificationsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Forget notification bell cache
     *
     * @param int $userId
     * @return void
     */
    public function forgetCache(int $userId)  {
        Cache::forget(CacheKey::CHAT_NOTIFICATIONS_BELL->value . $userId);
    }

    /**
     * Caches and returns the result of the number messages
     *
     * @param  array $arrayUserId
     * @param  bool $isClearCache
     *
     * @return void
     */
    public function updateUserNotificationsCache(
        array|Collection $arrayUserId = [],
        bool $isClearCache = false
    ): void {

        if (!auth()->check()) {
            return;
        }

        $arrayUserId = is_array($arrayUserId) ? $arrayUserId : $arrayUserId->all();

        if (empty($arrayUserId)) {
            $arrayUserId[] = auth()->id();
        }

        if ($isClearCache) {
            foreach ($arrayUserId as $userId) {
                $this->forgetCache($userId);
            }
        }

        foreach ($arrayUserId as $userId) {
            cache()->remember(
                CacheKey::CHAT_NOTIFICATIONS_BELL->value . $userId,
                now()->addMinutes(5),
                fn () => $this->repository->getUserMessagesNotifications($userId)
            );
        }
    }
}
