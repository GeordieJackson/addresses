<?php

namespace GeordieJackson\Address\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \GeordieJackson\Address\Address
 */
class Address extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'addresses';
    }
}
