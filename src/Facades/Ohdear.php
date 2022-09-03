<?php

namespace abenevaut\Ohdear\Facades;

use abenevaut\Ohdear\Contracts\OhdearProviderNameInterface;
use Illuminate\Support\Facades\Facade;

class Ohdear extends Facade implements OhdearProviderNameInterface
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return self::OHDEAR;
    }
}
