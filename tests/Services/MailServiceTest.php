<?php
namespace EnzanRocket\Foundation\Tests\Services;

use EnzanRocket\Foundation\Services\MailServiceInterface;
use EnzanRocket\Foundation\Tests\TestCase;

class MailServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var MailServiceInterface $service */
        $service = app()->make(MailServiceInterface::class);
        $this->assertNotNull($service);
    }
}
