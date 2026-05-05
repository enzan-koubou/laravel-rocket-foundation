<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class CountryHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \EnzanRocket\Foundation\Helpers\DataHelperInterface::class;
    }
}
