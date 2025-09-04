<?php

namespace App\Contracts\Chat\Notifications;

/**
 * Interface MessageNotificationsRepositoryInterface
 *
 * @package App\Contracts\Chat\Notifications
 */
interface MessageNotificationsRepositoryInterface
{
    public function getUserMessagesNotifications(int $userId);
}
