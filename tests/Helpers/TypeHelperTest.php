<?php
namespace EnzanRocket\Foundation\Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class TypeHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\TypeHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\TypeHelperInterface::class);
        $this->assertNotNull($helper);
    }
}
