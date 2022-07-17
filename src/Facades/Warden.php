<?php

namespace Jeffwhansen\Warden\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffwhansen\Warden\Warden
 */
class Warden extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-warden';
    }
}
