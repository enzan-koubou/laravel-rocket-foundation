<?php
namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\FileUploadServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var FileUploadService $service */
        $service = app()->make(FileUploadServiceInterface::class);
        $this->assertNotNull($service);
    }
}
