<?php
namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\FileUploadS3ServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class FileUploadS3ServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var FileUploadService $service */
        $service = app()->make(FileUploadS3ServiceInterface::class);
        $this->assertNotNull($service);
    }
}
