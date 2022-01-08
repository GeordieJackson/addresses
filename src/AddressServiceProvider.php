<?php

namespace GeordieJackson\Address;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use GeordieJackson\Address\Commands\AddressCommand;

class AddressServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('addresses')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_addresses_table')
            ->hasCommand(AddressCommand::class);
    }
}
