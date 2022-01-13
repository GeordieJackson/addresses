<?php

namespace GeordieJackson\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \GeordieJackson\Addresses\Address
 */
class Address extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'addresses';
    }
}
