<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ChatMessageNotifyEvent;
use Illuminate\Support\Facades\Notification;
use App\User;

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
    public function handle(ChatMessageNotifyEvent $event)
    {
        // TODO Add a check if notification is enabled for a user
        $recive = User::where('id', $event->messageObj->recive)
            ->firstOrFail();

        if (empty($recive->email)) {
            return;
        }

        $recive->text = $event->messageObj->text;
        $recive->action = $this->actionDialog . $event->messageObj->dialog;

        //Notification::send($recive, new \App\Notifications\ChatMessageNorify());
    }
}
