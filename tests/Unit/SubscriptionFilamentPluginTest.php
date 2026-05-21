<?php

use Lalalili\SubscriptionFilament\SubscriptionFilamentPlugin;

it('uses a stable plugin id', function (): void {
    expect(SubscriptionFilamentPlugin::make()->getId())->toBe('subscription');
});
