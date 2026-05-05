<?php
namespace EnzanRocket\Foundation\Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class StringHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\StringHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\StringHelperInterface::class);
        $this->assertNotNull($helper);
    }

    public function testRandomString()
    {
        /** @var \EnzanRocket\Foundation\Helpers\StringHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\StringHelperInterface::class);
        $string = $helper->randomString(10);
        $this->assertEquals(10, strlen($string));

        $anotherString = $helper->randomString(10);
        $this->assertNotEquals($anotherString, $string);
    }
}
