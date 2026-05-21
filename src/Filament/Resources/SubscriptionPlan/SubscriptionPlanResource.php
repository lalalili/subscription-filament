<?php

namespace Lalalili\SubscriptionFilament\Filament\Resources\SubscriptionPlan;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Lalalili\SubscriptionCore\Models\SubscriptionPlan;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $slug = 'subscription-plans';

    protected static ?string $navigationLabel = '訂閱方案';

    protected static ?string $modelLabel = '訂閱方案';

    public static function getNavigationGroup(): ?string
    {
        return config('subscription-filament.navigation_group', '訂閱管理');
    }

    public static function getNavigationSort(): ?int
    {
        return config('subscription-filament.plans_navigation_sort', 10);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('方案名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('方案描述')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('monthly_price')
                    ->label('月費 (NT$)')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Forms\Components\TextInput::make('yearly_price')
                    ->label('年費 (NT$)')
                    ->numeric()
                    ->nullable()
                    ->minValue(0),
                Forms\Components\TextInput::make('product_limit')
                    ->label('商品數上限（舊欄位）')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Forms\Components\TextInput::make('monthly_api_limit')
                    ->label('每月 API 呼叫上限（舊欄位）')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Forms\Components\KeyValue::make('features.features')
                    ->label('功能開關')
                    ->helperText('例：survey.advanced_fields = true')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('features.limits')
                    ->label('用量限制')
                    ->helperText('例：recommendation.products = 5000、recommendation.monthly_api_calls = 100000')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('上架')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('方案名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monthly_price')
                    ->label('月費')
                    ->numeric()
                    ->prefix('NT$ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('yearly_price')
                    ->label('年費')
                    ->numeric()
                    ->prefix('NT$ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_limit')
                    ->label('商品上限')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthly_api_limit')
                    ->label('API 上限')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('上架'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscriptionPlans::route('/'),
        ];
    }
}
