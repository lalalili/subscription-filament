<?php

namespace Lalalili\SubscriptionFilament\Filament\Resources\SubscriptionPlan\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Lalalili\SubscriptionFilament\Filament\Resources\SubscriptionPlan\SubscriptionPlanResource;

class ManageSubscriptionPlans extends ManageRecords
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
