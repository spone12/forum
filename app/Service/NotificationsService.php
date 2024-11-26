<?php

namespace App\Service;

use App\Repository\NotificationsRepository;
use App\Traits\ArrayHelper;
use Illuminate\Support\Facades\Auth;
use App\Enums\TimeEnums;
use Illuminate\Support\Facades\Cache;

/**
 * Class HomeService
 * @package App\Service
 */
class NotificationsService
{
    use ArrayHelper;

    const CACHE_NAME = 'userNorificationsBell';

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
        Cache::forget(self::CACHE_NAME . $userId);
    }

    /**
     * Caches and returns the result of the number messages
     *
     * @param  int $userId
     * @param  bool $isClearCache
     * @return void
     */
    public static function userNotifications(int $userId = null, bool $isClearCache = false): void
    {

        if (Auth::check()) {

            if ($userId === null) {
                $userId = Auth::user()->id;
            }

            if ($isClearCache) {
                self::forgetNotificationCache($userId);
            }

            cache()->remember(self::CACHE_NAME . $userId, TimeEnums::DAY, function () use ($userId)   {
                $userNotifications = NotificationsRepository::getUserNotifications($userId);
                ArrayHelper::noAvatar($userNotifications);
                return $userNotifications;
            });
        }
    }
}
