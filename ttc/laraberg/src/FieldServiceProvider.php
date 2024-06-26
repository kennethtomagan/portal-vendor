<?php

namespace Ttc\Laraberg;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

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
            Nova::script('react', 'https://unpkg.com/react@17.0.2/umd/react.production.min.js');
            Nova::script('react-dom', 'https://unpkg.com/react-dom@17.0.2/umd/react-dom.production.min.js');
            Nova::style('laraberg-field', __DIR__ . '/../dist/css/field.css');
            Nova::script('laraberg', __DIR__ . '/../dist/js/field.js');
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
}
