<?php

namespace App\Filament\Distributor\Resources;

use App\Filament\Distributor\Resources\OrderResource\Pages;
use App\Filament\Distributor\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-m-bolt';

    public static function getNavigationBadge(): ?string
    {
        $distributorID = auth()->id();
        return static::getModel()::where('delivery_status', 'pending')
            ->where('pay_status', 'PAID')
            ->where('distributor_id', $distributorID)->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unprocessed Orders';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $distributorID = auth()->id();
        $pendingCount = static::getModel()::where('delivery_status', 'pending')
            ->where('pay_status', 'PAID')
            ->where('distributor_id', $distributorID)->count();
        return $pendingCount > 0 ? 'danger' : 'primary';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $distributorID = $user->id;
        
        return parent::getEloquentQuery()->where('distributor_id', $distributorID);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pay_status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DUE' => 'danger',
                        'PAID' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('delivery_status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'sent' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('grand_total')
                    ->prefix('RM ')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agent_comm')
                    ->prefix('RM ')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable()
            ])
            ->filters([
                Filters\SelectFilter::make('pay_status')
                    ->options([
                    'PAID' => 'Paid',
                    'DUE' => 'Due',
                ]),
                Filters\SelectFilter::make('delivery_status')
                    ->label('Delivery')
                    ->options([
                    'sent' => 'Sent',
                    'pending' => 'Pending',
                ])
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/view')
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Customer Details')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name')
                            ->label('Name'),
                        Infolists\Components\TextEntry::make('customer_phone')
                            ->label('Phone'),
                        Infolists\Components\TextEntry::make('customer_email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('address')
                            ->columnSpanFull()
                    ]),
                Infolists\Components\Section::make('Order Data')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('processOrder')
                            ->label('Process Order')
                            ->requiresConfirmation()
                            ->modalDescription('This action will mark the order as processed.')
                            ->icon('heroicon-s-truck')
                            ->color('info')
                            ->action(function(Order $record){
                                $record->delivery_status = 'sent';
                                $record->save();

                                Notification::make()
                                    ->title('Order Processed')
                                    ->body('Order has been marked as processed.')
                                    ->success()
                                    ->color('success')
                                    ->send();
                            })
                            ->hidden(fn(Order $record)=> $record->delivery_status == 'sent'),
                        Infolists\Components\Actions\Action::make('unprocessOrder')
                            ->label('Unprocess Order')
                            ->requiresConfirmation()
                            ->modalDescription('This action will mark the order as not processed.')
                            ->icon('heroicon-s-stop')
                            ->color('warning')
                            ->action(function(Order $record){
                                $record->delivery_status = 'pending';
                                $record->save();

                                Notification::make()
                                    ->title('Order Unprocessed')
                                    ->body('Order has been marked as un-processed.')
                                    ->info()
                                    ->color('info')
                                    ->send();
                            })
                            ->hidden(fn(Order $record)=> $record->delivery_status == 'pending')
                    ])
                    ->columns(4)
                    ->schema([
                        Infolists\Components\TextEntry::make('grand_total')
                            ->label('Total')
                            ->prefix('RM '),
                        Infolists\Components\TextEntry::make('agent_comm')
                            ->label('Agent Commission')
                            ->prefix('RM '),
                        Infolists\Components\TextEntry::make('pay_status')
                            ->label('Pay Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'PAID' => 'success',
                                'DUE' => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('delivery_status')
                            ->label('Order Processed')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'sent' => 'success',
                                'pending' => 'danger',
                                default => 'danger'
                            }),
                        Infolists\Components\TextEntry::make('pay_at')
                            ->label('Paid On')
                            ->date('d/m/Y g:i A'),
                        Infolists\Components\TextEntry::make('fpx_ref')
                            ->label('ToyyibPay Ref')
                    ])
            ]);
    }
}
