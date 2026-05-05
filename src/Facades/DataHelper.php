<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;
use EnzanRocket\Foundation\Helpers\DataHelperInterface;

class DataHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DataHelperInterface::class;
    }
}
