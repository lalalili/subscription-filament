<?php

namespace Lalalili\SubscriptionFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Lalalili\SubscriptionFilament\Filament\Resources\Subscription\SubscriptionResource;
use Lalalili\SubscriptionFilament\Filament\Resources\SubscriptionPlan\SubscriptionPlanResource;

class SubscriptionFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'subscription';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            SubscriptionPlanResource::class,
            SubscriptionResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
