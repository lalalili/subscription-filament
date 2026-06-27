<?php

namespace Lalalili\SubscriptionFilament\Filament\Resources\Subscription;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lalalili\SubscriptionCore\Contracts\SubscriptionCanceller;
use Lalalili\SubscriptionCore\Enums\SubscriptionStatus;
use Lalalili\SubscriptionCore\Models\Subscription;
use Lalalili\SubscriptionCore\Services\SubscriptionService;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $slug = 'merchant-subscriptions';

    protected static ?string $navigationLabel = '訂閱記錄';

    protected static ?string $modelLabel = '訂閱記錄';

    public static function getNavigationGroup(): ?string
    {
        return config('subscription-filament.navigation_group', '訂閱管理');
    }

    public static function getNavigationSort(): ?int
    {
        return config('subscription-filament.subscriptions_navigation_sort', 11);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('status')
                    ->label('狀態')
                    ->options(SubscriptionStatus::class)
                    ->required(),
                Forms\Components\Toggle::make('is_internal')
                    ->label('內部訂閱')
                    ->helperText('內部訂閱永不過期，並可依設定套用 unlimited limits。'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $ownerColumn = 'owner.'.config('subscription.owner.display_column', 'name');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make($ownerColumn)
                    ->label('訂閱對象')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('方案')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('週期')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->label('金額')
                    ->numeric()
                    ->prefix('NT$ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('狀態')
                    ->badge(),
                Tables\Columns\TextColumn::make('is_internal')
                    ->label('訂閱類型')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? '內部' : '一般')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('定期定額')
                    ->boolean(),
                Tables\Columns\TextColumn::make('total_success_times')
                    ->label('已扣款期數')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('failed_charge_count')
                    ->label('連續失敗')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('gwsr')
                    ->label('綠界授權單號')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('下次扣款日')
                    ->state(fn (Subscription $record): string => $record->getAttribute('is_recurring') && ! $record->getAttribute('is_internal')
                        ? (string) $record->getAttribute('expires_at')?->format('Y-m-d')
                        : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('生效日')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('到期日')
                    ->state(fn (Subscription $record): string => $record->getAttribute('is_internal') ? '永不過期' : (string) $record->getAttribute('expires_at')?->format('Y-m-d'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('狀態')
                    ->options(SubscriptionStatus::class),
                Tables\Filters\Filter::make('is_internal')
                    ->label('內部訂閱')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_internal', true)),
                Tables\Filters\Filter::make('is_recurring')
                    ->label('定期定額')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_recurring', true)),
            ])
            ->recordActions([
                Actions\Action::make('cancel')
                    ->label('取消訂閱')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('取消訂閱')
                    ->modalDescription('將停止後續自動扣款（定期定額會連動綠界取消），並把訂閱標記為已取消。')
                    ->visible(fn (Subscription $record): bool => $record->status === SubscriptionStatus::Active && ! $record->is_internal)
                    ->action(function (Subscription $record): void {
                        if (static::cancelSubscription($record)) {
                            Notification::make()->title('訂閱已取消')->success()->send();

                            return;
                        }

                        Notification::make()->title('綠界取消定期定額失敗，請稍後再試')->danger()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscriptions::route('/'),
        ];
    }

    /**
     * 取消訂閱：優先使用 host 綁定的 SubscriptionCanceller（含金流連動取消）；
     * 未綁定時退回僅標記本地取消，避免套件硬耦合特定金流。
     *
     * @return bool 取消成功為 true；金流取消失敗為 false
     */
    protected static function cancelSubscription(Subscription $subscription): bool
    {
        if (app()->bound(SubscriptionCanceller::class)) {
            return app(SubscriptionCanceller::class)->cancel($subscription);
        }

        app(SubscriptionService::class)->cancelSubscription($subscription);

        return true;
    }
}
