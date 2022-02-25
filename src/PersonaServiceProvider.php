<?php

namespace Doinc\PersonaKyc;

use Doinc\PersonaKyc\Commands\UpdateCommand;
use Doinc\PersonaKyc\Providers\EventServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Doinc\PersonaKyc\Commands\InstallationCommand;

class PersonaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-persona-kyc')
            ->hasConfigFile()
            ->hasRoute("webhook")
            ->hasMigration("create_persona_events")
            ->hasCommands([
                InstallationCommand::class,
                UpdateCommand::class
            ]);
    }

    public function packageRegistered()
    {
        parent::packageRegistered();

        $this->app->register(EventServiceProvider::class);
    }
}
