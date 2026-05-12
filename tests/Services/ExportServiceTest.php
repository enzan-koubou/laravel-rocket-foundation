<?php

namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\ExportServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class ExportServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var \EnzanRocket\Foundation\Services\ExportServiceInterface $service */
        $service = app()->make(ExportServiceInterface::class);
        $this->assertNotNull($service);
    }
}
