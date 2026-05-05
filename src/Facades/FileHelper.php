<?php
namespace EnzanRocket\Foundation\Facades;

use Illuminate\Support\Facades\Facade;
use EnzanRocket\Foundation\Helpers\FileHelperInterface;

class FileHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FileHelperInterface::class;
    }
}
