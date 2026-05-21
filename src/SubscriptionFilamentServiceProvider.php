<?php

namespace Lalalili\SubscriptionFilament;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SubscriptionFilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('subscription-filament')
            ->hasConfigFile('subscription-filament');
    }
}
