<?php


namespace TwoBeeSolution\ArubaPec;

use Illuminate\Support\ServiceProvider;


class ArubaPecServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/aruba-pec.php' => config_path('aruba-pec.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../config/aruba-pec.php', 'aruba-pec'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}