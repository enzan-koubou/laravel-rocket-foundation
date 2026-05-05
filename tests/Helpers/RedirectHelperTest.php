<?php
namespace EnzanRocket\Foundation\Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class RedirectHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\RedirectHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\RedirectHelperInterface::class);
        $this->assertNotNull($helper);
    }
}
