<?php

namespace LaraWave\LogicAsData\Tests;

use LaraWave\LogicAsData\LogicAsDataCoreServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LogicAsDataCoreServiceProvider::class,
        ];
    }

    /**
     * Automatically run migrations for every test.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
