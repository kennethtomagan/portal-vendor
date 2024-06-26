<?php

namespace Ttc\ResourceLoader;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Ttc\GlobalConfig\Http\Middleware\Authorize;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('resource-loader', __DIR__.'/../dist/js/field.js');
            Nova::style('resource-loader', __DIR__.'/../dist/css/field.css');
        });
    }

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
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }


        // have to use this weird route as nova-api matches 'nova-api/{resource}/{resourceId}/field/{field}'
        // which overrides our call.
        // in tool.vue we set the url to be used by fields


        Nova::router(['nova', Authenticate::class, Authorize::class], 'global-config')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/global-config')
            ->group(__DIR__.'/../routes/api.php');

    }

}
