<?php

namespace App\Service;

use App\Repository\NotificationsRepository;
use App\Enums\TimeEnums;
use App\Enums\Cache\CacheKey;
use Illuminate\Support\Facades\Cache;

/**
 * Class HomeService
 * @package App\Service
 */
class NotificationsService
{
    /** @var NotificationsRepository */
    protected $notificaRepository;

    /**
     * NotificationsRepository constructor.
     *
     * @param NotificationsRepository $notificaRepository
     */
    function __construct(NotificationsRepository $notificaRepository) {
        $this->notificaRepository = $notificaRepository;
    }

    /**
     * Forget notification bell cache
     *
     * @param int $userId
     * @return void
     */
    public static function forgetNotificationCache(int $userId)  {
        Cache::forget(CacheKey::CHAT_NOTIFICATIONS_BELL->value . $userId);
    }

    /**
     * Caches and returns the result of the number messages
     *
     * @param  int $userId
     * @param  bool $isClearCache
     *
     * @return void
     */
    public static function updateUserNotificationsCache(int $userId = null, bool $isClearCache = false): void
    {
        if ($userId === null) {
            if (!auth()->check()) {
                return;
            }
            $userId = auth()->id();
        }

        if ($isClearCache) {
            self::forgetNotificationCache($userId);
        }

        cache()->remember(
            CacheKey::CHAT_NOTIFICATIONS_BELL->value . $userId,
            now()->addMinutes(5),
            fn () => NotificationsRepository::getUserNotifications($userId)
        );
    }
}
