<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class StringHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EnzanRocket\Foundation\Helpers\StringHelperInterface';
    }
}
