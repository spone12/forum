<?php

namespace App\Listeners;

use App\Events\ChatMessageEvent;
use App\Service\Chat\Notifications\MessageNotificationsService;

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
        app(MessageNotificationsService::class)
            ->forgetCache($event->messageObj->user_id);

        // TODO Add a check if notification is enabled for a user
    }
}
