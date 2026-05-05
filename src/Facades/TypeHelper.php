<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;
use EnzanRocket\Foundation\Helpers\TypeHelperInterface;

class TypeHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TypeHelperInterface::class;
    }
}
