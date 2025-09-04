<?php

use App\Broadcasting\ChatChannel;
use App\Enums\Broadcast\ChannelEnum;

Broadcast::channel(ChannelEnum::CHAT->value . '.{dialogId}', ChatChannel::class);
