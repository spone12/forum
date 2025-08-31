<?php

namespace App\Listeners;

use App\Enums\Cache\CacheKey;
use App\Events\ChatMessageEvent;
use App\User;
use Illuminate\Support\Facades\Cache;

class ChatMessageNotifyListener
{
    protected $actionDialog = '/chat/dialog/';

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ChatMessageEvent $event)
    {
        // Clear the number of notifications for a user
        Cache::forget(CacheKey::CHAT_NOTIFICATIONS_BELL->value . $event->messageObj->user_id);

        // TODO Add a check if notification is enabled for a user
    }
}
