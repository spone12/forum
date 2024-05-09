<?php

namespace App\Events;

use App\Models\Chat\MessagesModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\User;

class ChatMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageObj;
    public $userObj;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $userObj, MessagesModel $messageObj)
    {
        $this->messageObj = $messageObj;
        $this->userObj = $userObj;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn() {
        return new PrivateChannel('chat.' . $this->messageObj->dialog);
    }
}
