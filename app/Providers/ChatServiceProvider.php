<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\Chat\{ChatMessageSearchInterface};
use App\Contracts\Chat\Dialog\{DialogCommandRepositoryInterface, DialogQueryRepositoryInterface};
use App\Contracts\Chat\Messages\{MessageCommandRepositoryInterface, MessageQueryRepositoryInterface};
use App\Contracts\Chat\Notifications\MessageNotificationsRepositoryInterface;

use App\Repository\Chat\{ChatSearchRepository};
use App\Repository\Chat\Dialog\{DialogCommandRepository, DialogQueryRepository};
use App\Repository\Chat\Messages\{MessageQueryRepository, MessageCommandRepository};
use App\Repository\Chat\Notifications\MessageNotificationsRepository;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MessageCommandRepositoryInterface::class, MessageCommandRepository::class);
        $this->app->bind(ChatMessageSearchInterface::class, ChatSearchRepository::class);
        $this->app->bind(DialogCommandRepositoryInterface::class, DialogCommandRepository::class);
        $this->app->bind(DialogQueryRepositoryInterface::class, DialogQueryRepository::class);
        $this->app->bind(MessageQueryRepositoryInterface::class, MessageQueryRepository::class);
        $this->app->bind(MessageNotificationsRepositoryInterface::class, MessageNotificationsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
