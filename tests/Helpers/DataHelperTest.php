<?php
namespace EnzanRocket\Foundation\Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class DataHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\DataHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\DataHelperInterface::class);
        $this->assertNotNull($helper);
    }

    public function testGetCountryName()
    {
        /** @var \EnzanRocket\Foundation\Helpers\DataHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\DataHelperInterface::class);

        $result = $helper->getCountryName('JPN', 'TEST');
        $this->assertEquals('TEST', $result);
    }

    public function testGetCurrencyName()
    {
        /** @var \EnzanRocket\Foundation\Helpers\DataHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\DataHelperInterface::class);

        $result = $helper->getCurrencyName('JPY', 'TEST');
        $this->assertEquals('TEST', $result);
    }
}
