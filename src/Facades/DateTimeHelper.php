<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class DateTimeHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EnzanRocket\Foundation\Helpers\DateTimeHelperInterface';
    }
}
