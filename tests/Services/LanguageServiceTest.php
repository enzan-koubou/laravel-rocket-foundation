<?php
namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\LanguageServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class LanguageServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var LanguageServiceInterface $service */
        $service = app()->make(LanguageServiceInterface::class);
        $this->assertNotNull($service);
    }
}
