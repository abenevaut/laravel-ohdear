<?php

namespace abenevaut\Ohdear\Factories;

use abenevaut\Ohdear\Contracts\ApiRepositoryAbstract;
use abenevaut\Ohdear\Contracts\OhdearDriversEnum;
use Illuminate\Foundation\Application;

class OhdearDriverFactory
{
    /**
     * @param  Application  $app
     */
    public function __construct(private Application $app)
    {
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public function request(OhdearDriversEnum $driver): ApiRepositoryAbstract
    {
        return $this
            ->app
            ->make('\\abenevaut\\Ohdear\\Repositories\\'.$driver->value.'Repository', [
                'accessToken' => $this->app['config']->get('services.ohdear.access_token'),
                'debug' => $this->app->hasDebugModeEnabled(),
            ]);
    }
}
