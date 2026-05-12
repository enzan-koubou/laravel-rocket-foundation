<?php

namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\FileUploadLocalServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class FileUploadLocalServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var FileUploadLocalServiceInterface $service */
        $service = app()->make(FileUploadLocalServiceInterface::class);
        $this->assertNotNull($service);
    }
}
