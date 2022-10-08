<?php

namespace abenevaut\Ohdear\Providers;

use abenevaut\Ohdear\Contracts\OhdearEntitiesEnum;
use abenevaut\Ohdear\Contracts\OhdearProviderNameInterface;
use abenevaut\Ohdear\Factories\OhdearDriverFactory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
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
        Route::group([
            'as' => 'ohdear.',
            'namespace' => 'abenevaut\Ohdear\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });

        Collection::macro('toOhdearEntity', function (OhdearEntitiesEnum $driver) {
            return $this->map(function ($value) use ($driver) {
                return new ("abenevaut\\Ohdear\\Entities\\{$driver->value}Entity")($value);
            });
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(self::OHDEAR, function (Application $app) {
            // @codeCoverageIgnoreStart
            return new OhdearDriverFactory($app);
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
