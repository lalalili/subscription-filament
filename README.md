# Subscription Filament

Filament admin UI layer for `lalalili/subscription-core`.

`lalalili/subscription-filament` registers resources for managing subscription plans and subscriptions.

## Requirements

- PHP 8.2+
- Laravel 11 / 12 / 13
- Filament 4 / 5
- `lalalili/subscription-core`

The current aitehub host uses Laravel 13 and Filament 5.

## Installation

```bash
composer require lalalili/subscription-filament
php artisan vendor:publish --tag=subscription-filament-config
```

For GitHub installs before a Packagist release:

```json
{
    "repositories": [
        {"type": "vcs", "url": "https://github.com/lalalili/subscription-core.git"},
        {"type": "vcs", "url": "https://github.com/lalalili/subscription-filament.git"}
    ]
}
```

## Enable Plugin

Register the plugin in a Filament panel provider:

```php
use Lalalili\SubscriptionFilament\SubscriptionFilamentPlugin;

$panel->plugins([
    SubscriptionFilamentPlugin::make(),
]);
```

## Configuration

`config/subscription-filament.php` controls:

- `navigation_group`
- `plans_navigation_sort`
- `subscriptions_navigation_sort`

## Resources

- `SubscriptionPlanResource`
- `SubscriptionResource`

## Boundaries

- This package owns Filament resources only.
- Subscription models, services, feature checks, policies, and expiration commands live in `lalalili/subscription-core`.
- Host applications own panel registration, authorization policy decisions, and payment provider integration.

## Tests

From the package directory:

```bash
./vendor/bin/pest
```
