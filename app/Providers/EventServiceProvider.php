<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Observers\MessageObserver;
use App\Models\Chat\MessagesModel;
use App\Events\ChatMessageNotifyEvent;
use App\Listeners\ChatMessageNotifyListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\AddDataToUserSession',
        ],

        ChatMessageNotifyEvent::class => [
            ChatMessageNotifyListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        \App\Models\Notation\NotationModel::observe(
            new \App\Observers\NotationsObserver()
        );
    }
}
