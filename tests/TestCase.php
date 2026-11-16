<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Env;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->guardAgainstProductionEnvironment();

        parent::setUp();

        $this->assertSafeTestEnvironment();
    }

    /**
     * Abort *before* the framework boots, so a misconfigured test run can
     * never reach the production database (RefreshDatabase would otherwise
     * run `migrate:fresh` before any assertion in the test body).
     */
    private function guardAgainstProductionEnvironment(): void
    {
        $environment = Env::get('APP_ENV');
        $connection = Env::get('DB_CONNECTION');
        $database = Env::get('DB_DATABASE');

        if ($environment !== 'testing' || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(sprintf(
                'Refusing to run tests: expected APP_ENV=testing, DB_CONNECTION=sqlite, DB_DATABASE=:memory: '
                .'but got APP_ENV=%s, DB_CONNECTION=%s, DB_DATABASE=%s. Run "sudo bash scripts/test.sh".',
                var_export($environment, true),
                var_export($connection, true),
                var_export($database, true)
            ));
        }

        // A cached config file bypasses phpunit.xml entirely and would point
        // the tests at the production MySQL database.
        $cachedConfig = $this->effectiveConfigCachePath();

        if (is_file($cachedConfig)) {
            throw new RuntimeException(sprintf(
                'Refusing to run tests: cached config "%s" would override the testing environment. '
                .'Run "php artisan config:clear" or "sudo bash scripts/test.sh".',
                $cachedConfig
            ));
        }
    }

    /**
     * Final safety net: verify what the booted application actually uses.
     */
    private function assertSafeTestEnvironment(): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if (! app()->environment('testing') || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(sprintf(
                'Refusing to run tests: application booted against %s:%s instead of sqlite::memory:.',
                var_export($connection, true),
                var_export($database, true)
            ));
        }
    }

    private function effectiveConfigCachePath(): string
    {
        $root = dirname(__DIR__);
        $configured = Env::get('APP_CONFIG_CACHE');

        if (! is_string($configured) || $configured === '') {
            return $root.'/bootstrap/cache/config.php';
        }

        $isAbsolute = str_starts_with($configured, '/')
            || str_starts_with($configured, '\\')
            || preg_match('#^[A-Za-z]:[\\\\/]#', $configured) === 1;

        return $isAbsolute ? $configured : $root.'/'.ltrim($configured, '/\\');
    }
}
