<?php
namespace EnzanRocket\Foundation\Tests\Helpers;

use EnzanRocket\Foundation\Tests\TestCase;

class CollectionHelperTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Helpers\CollectionHelperInterface $helper */
        $helper = app()->make(\EnzanRocket\Foundation\Helpers\CollectionHelperInterface::class);
        $this->assertNotNull($helper);
    }
}
