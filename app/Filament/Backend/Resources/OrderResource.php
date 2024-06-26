<?php

namespace App\Filament\Backend\Resources;

use App\Filament\Backend\Resources\OrderResource\Pages;
use App\Filament\Backend\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-m-bolt';

    public static function canCreate(): bool
    {
        return false;
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
                Tables\Columns\IconColumn::make('pay_status')
                    ->sortable()
                    ->icon(fn (string $state): string => match ($state) {
                        'DUE' => 'heroicon-o-x-circle',
                        'PAID' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'DUE' => 'danger',
                        'PAID' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->prefix('RM ')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('distributor.name')
                    ->label('Distributor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')
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
                    ->label('Processed')
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
            ->defaultSort('created_at','DESC');
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
