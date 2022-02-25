<?php

namespace Doinc\PersonaKyc\Tests;

use App\Providers\AppServiceProvider;
use Doinc\PersonaKyc\PersonaServiceProvider;
use Dotenv\Dotenv;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            PersonaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set("persona-kyc.api_key", env("PERSONA_API_KEY"));
        config()->set("persona-kyc.key_inflection", "snake");
    }

    protected function loadEnvironmentVariables()
    {
        if (!file_exists(__DIR__ . "/../.env")) {
            return;
        }

        $dotEnv = Dotenv::createImmutable(__DIR__ . "/..");
        $dotEnv->load();
    }
}
