<?php
namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\ImageServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class ImageServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var ImageServiceInterface $service */
        $service = app()->make(ImageServiceInterface::class);
        $this->assertNotNull($service);
    }
}
