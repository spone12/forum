<?php

namespace App\Enums\Cache;

enum CacheKey:string
{
    case CHAT_NOTIFICATIONS_BELL = 'user:chat:notifications:bell:';
    case USER_IS_ONLINE = 'user:is_online:';
}
