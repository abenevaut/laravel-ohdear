<?php

namespace abenevaut\Ohdear\Providers;

use abenevaut\Ohdear\Contracts\OhdearProviderNameInterface;
use abenevaut\Ohdear\Factories\OhdearDriverFactory;
use Illuminate\Support\ServiceProvider;

class OhdearServiceProvider extends ServiceProvider implements OhdearProviderNameInterface
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
            __DIR__.'/../../config/ohdear.php',
        ], self::OHDEAR);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(self::OHDEAR, function ($app) {
            // @codeCoverageIgnoreStart
            return new OhdearDriverFactory();
            // @codeCoverageIgnoreEnd
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [self::OHDEAR];
    }
}
