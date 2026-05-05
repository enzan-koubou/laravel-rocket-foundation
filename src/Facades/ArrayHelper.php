<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;
use EnzanRocket\Foundation\Helpers\ArrayHelperInterface;

class ArrayHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ArrayHelperInterface::class;
    }
}
