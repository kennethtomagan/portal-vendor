<?php

namespace Ttc\GlobalConfig;

use App\Http\Controllers\Shared\OrderFieldsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Ttc\GlobalConfig\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {

        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/global-config')
            ->group(__DIR__.'/../routes/api.php');

        //return [
        //            'domain' => config('nova.domain', null),
        //            'as' => 'nova.api.',
        //            'prefix' => 'nova-api',
        //            'middleware' => 'nova:api',
        //        ];

        Route::middleware(['nova:api'])
            ->prefix('nova-api')
            ->as('nova.api')
            ->get('/{resource}/{resourceId}/order-fields', OrderFieldsController::class);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
