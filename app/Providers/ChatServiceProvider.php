<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Chat\ChatMessageRepositoryInterface::class,
            \App\Repository\Chat\ChatMessageRepository::class
        );

        $this->app->bind(
            \App\Contracts\Chat\ChatMessageSearchInterface::class,
            \App\Repository\Chat\ChatSearchRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
