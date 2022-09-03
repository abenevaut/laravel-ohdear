<?php

namespace abenevaut\Ohdear\Factories;

use abenevaut\Ohdear\Contracts\ApiRepositoryAbstract;
use abenevaut\Ohdear\Contracts\OhdearDriversEnum;

class OhdearDriverFactory
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public function drive(OhdearDriversEnum $driver): ApiRepositoryAbstract
    {
        $class = '\\abenevaut\\Ohdear\\Repositories\\' . $driver->value . 'Repository';

        return new $class();
    }
}
