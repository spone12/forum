<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use \Blade;
use Illuminate\Support\Facades\Schema;

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

        //Директива @ifGuest' которая проверяет, является ли пользователем гостем
        //Сокращённый вариант <?php echo ? >
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

        //Директива с параметром, вставляет HTML код разрыва строки перед каждым переводом строки
        Blade::directive(
            'newlinesToBr', function ($expression) {
                return "<?php echo nl2br({$expression}); ?>";
            }
        );
    }
}
