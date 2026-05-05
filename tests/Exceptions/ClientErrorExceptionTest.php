<?php
namespace EnzanRocket\Foundation\Tests\Exceptions;

use EnzanRocket\Foundation\Exceptions\ClientErrorException;
use EnzanRocket\Foundation\Tests\TestCase;

class ClientErrorExceptionTest extends TestCase
{
    public function testCreateException()
    {
        $name      = str_random(10);
        $exception = new ClientErrorException($name);
        $this->assertNotEmpty($exception);

        $this->assertEquals($name, $exception->getError());
    }
}
