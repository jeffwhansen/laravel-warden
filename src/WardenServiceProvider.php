<?php

namespace Jeffwhansen\Warden;

use Jeffwhansen\Warden\Commands\WardenCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WardenServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-warden')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_warden_tables')
            ->hasCommand(WardenCommand::class);
    }
}
