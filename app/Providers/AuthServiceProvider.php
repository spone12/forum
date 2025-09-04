<?php

namespace App\Providers;

use App\Models\Chat\{
    DialogModel,
    MessagesModel
};
use App\Policies\Chat\{
    DialogPolicy,
    MessagesPolicy
};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        DialogModel::class => DialogPolicy::class,
        MessagesModel::class => MessagesPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
