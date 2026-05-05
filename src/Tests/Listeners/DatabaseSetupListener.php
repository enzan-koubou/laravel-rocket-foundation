<?php
namespace EnzanRocket\Foundation\Tests\Listeners;

use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * PHPUnit 11 extension that runs migrations before/after the test suite.
 *
 * Register in phpunit.xml under <extensions>:
 *   <extension class="EnzanRocket\Foundation\Tests\Listeners\DatabaseSetupListener"/>
 */
class DatabaseSetupListener implements Extension
{
    protected array $suites = ['Application Test Suite'];

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        // PHPUnit 11 does not provide a "before suite" hook via extensions
        // easily; migrations should be managed via RefreshDatabase trait instead.
        // This class is kept as a stub for backwards compatibility.
    }
}
