<?php

Broadcast::channel('chat.{dialogId}', \App\Broadcasting\ChatChannel::class);
