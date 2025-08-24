<?php

namespace App\Service;

use App\Repository\NotificationsRepository;
use App\Traits\ArrayHelper;
use Illuminate\Support\Facades\Auth;
use App\Enums\TimeEnums;
use App\Enums\Cache\CacheKey;
use Illuminate\Support\Facades\Cache;

/**
 * Class HomeService
 * @package App\Service
 */
class NotificationsService
{
    use ArrayHelper;

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
    public static function userNotifications(int $userId = null, bool $isClearCache = false): void
    {
        if ($userId === null) {
            if (!Auth::check()) {
                return;
            }
            $userId = Auth::id();
        }

        if ($isClearCache) {
            self::forgetNotificationCache($userId);
        }

        cache()->remember(
            CacheKey::CHAT_NOTIFICATIONS_BELL->value . $userId,
            TimeEnums::DAY->value,
            function () use ($userId) {
                $userNotifications = NotificationsRepository::getUserNotifications($userId);
                ArrayHelper::noAvatar($userNotifications);
                return $userNotifications;
            }
        );
    }
}
