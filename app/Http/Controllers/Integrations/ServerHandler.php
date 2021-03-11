<?php

namespace App\Http\Controllers\Integrations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use VK\CallbackApi\Server\VKCallbackApiServerHandler;
use VK\Client\VKApiClient;

class ServerHandler extends VKCallbackApiServerHandler
{
    const CONFIRMATION_TOKEN = '25e22f56';
    const SECRET = 'ge34gjaSSg';
    const GROUP_ID = 203158449;

    protected $chatId;
    protected $text;

    function confirmation(int $group_id, ?string $secret) {
        Log::info(print_r($group_id, true));
        if ($secret === static::SECRET && $group_id === static::GROUP_ID) {
            echo static::CONFIRMATION_TOKEN;
        }
    }
    
    public function messageNew(int $group_id, ?string $secret, array $object) {
        echo 'ok';
    }
}
