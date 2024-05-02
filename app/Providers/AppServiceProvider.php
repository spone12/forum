<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use \Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Retrieving notification data into view from the cache
        view()->composer('layouts.app', function($view) {
            if (Auth::check()) {
                $view->with('userNorifications', cache()->get('userNorificationsBell' . Auth::user()->id));
            } else {
                $view->with('userNorifications', []);
            }
            
        });
        

        // Directive @ifGuest', that checks if the user is a guest
        Blade::if(
            'ifGuest', function () {
                return auth()->guest();
            }
        );

        Blade::directive(
            'getSessValue', function ($variable) {
                return "<?php echo session({$variable}); ?>";
            }
        );

        // Directive with parameter, inserts HTML line break code before each line break
        Blade::directive(
            'newlinesToBr', function ($expression) {
                return "<?php echo nl2br({$expression}); ?>";
            }
        );
    }
}
