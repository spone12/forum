<?php

namespace App\Enums\Cache;

enum CacheKey:string
{
    case CHAT_NOTIFICATIONS_BELL = 'user:chat:notifications:bell:';
}
