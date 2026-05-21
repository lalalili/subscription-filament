<?php

namespace Lalalili\SubscriptionFilament\Tests;

use Lalalili\SubscriptionCore\SubscriptionCoreServiceProvider;
use Lalalili\SubscriptionFilament\SubscriptionFilamentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SubscriptionCoreServiceProvider::class,
            SubscriptionFilamentServiceProvider::class,
        ];
    }
}
