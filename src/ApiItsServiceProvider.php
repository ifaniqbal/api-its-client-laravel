<?php
namespace Ifaniqbal\ApiIts;

use Ifaniqbal\ApiIts\ApiIts;
use Illuminate\Support\ServiceProvider;

class ApiItsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/api-its.php' => config_path('api-its.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/api-its.php',
            'api-its'
        );

        $this->app->singleton('api-its', function () {
            return new ApiIts();
        });
    }
}