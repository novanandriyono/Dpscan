<?php

namespace Dpscan;

use Illuminate\Support\ServiceProvider;

class DpscanServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $source = realpath($raw = __DIR__ . '/config/dpscan.php') ?: $raw;
        $this->publishes([$source => config_path('dpscan.php')],'dpscan.config');
        (config('dpscan') !== null)?:$this->mergeConfigFrom($source, 'dpscan');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Dpscan', function ($app) {
            $app->bind('Dpscan\Contracts\Dpscan',function(){
                return new Dpscan();
            });
            return $app->make('Dpscan\Contracts\Dpscan');
        });
    }
}
