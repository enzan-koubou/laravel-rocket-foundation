<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class PaginationHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EnzanRocket\Foundation\Helpers\PaginationHelperInterface';
    }
}
