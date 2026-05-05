<?php
namespace EnzanRocket\Foundation\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var bool */
    protected bool $useDatabase = false;

    /**
     * Setup DB before each test.
     */
    public function setUp(): void
    {
        parent::setUp();
        if ($this->useDatabase) {
            \DB::disableQueryLog();
            $this->truncateTables();
            $this->artisan('db:seed');
        }
    }

    public function tearDown(): void
    {
        if ($this->useDatabase) {
            \DB::disconnect();
        }

        parent::tearDown();
    }

    protected function truncateTables(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $databaseName = \DB::connection()->getDatabaseName();
        $tables       = \DB::select('SHOW TABLES');
        $keyName      = 'Tables_in_' . $databaseName;
        foreach ($tables as $table) {
            if (property_exists($table, $keyName)) {
                \DB::table($table->$keyName)->truncate();
            }
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
