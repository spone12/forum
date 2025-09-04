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
use App\Enums\Broadcast\ChannelEnum;
use App\User;

class ChatMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var MessagesModel $messageObj */
    public $messageObj;
    /** @var User $userObj */
    public $userObj;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MessagesModel $messageObj)
    {
        $this->messageObj = $messageObj;
        $this->userObj = auth()->user()->only(['id', 'name', 'avatar']);
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn() {
        return new PrivateChannel(ChannelEnum::CHAT->value . '.' . $this->messageObj->dialog_id);
    }
}
