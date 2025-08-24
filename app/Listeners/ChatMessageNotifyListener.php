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
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ChatMessageEvent $event)
    {
        // Clear user recive count notifications
        Cache::forget(CacheKey::CHAT_NOTIFICATIONS_BELL->value . $event->messageObj->recive);

       /* // TODO Add a check if notification is enabled for a user
        $recive = User::where('id', $event->messageObj->recive)
            ->firstOrFail();

        if (empty($recive->email)) {
            return;
        }

        $recive->text = $event->messageObj->text;
        $recive->action = $this->actionDialog . $event->messageObj->dialog;*/

        //Notification::send($recive, new \App\Notifications\ChatMessageNorify());
    }
}
