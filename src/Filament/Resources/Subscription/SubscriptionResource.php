<?php

namespace Lalalili\SubscriptionFilament\Filament\Resources\Subscription;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lalalili\SubscriptionCore\Enums\SubscriptionStatus;
use Lalalili\SubscriptionCore\Models\Subscription;

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
            ])
            ->recordActions([
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
}
