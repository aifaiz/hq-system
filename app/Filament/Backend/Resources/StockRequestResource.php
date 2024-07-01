<?php

namespace App\Filament\Backend\Resources;

use App\Filament\Backend\Resources\StockRequestResource\Pages;
use App\Filament\Backend\Resources\StockRequestResource\RelationManagers;
use App\Models\DistributorUser;
use App\Models\StockRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;

class StockRequestResource extends Resource
{
    protected static ?string $model = StockRequest::class;

    protected static ?string $navigationIcon = 'heroicon-c-tag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('deliver_status', 'pending')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unprocessed Request';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'danger' : 'primary';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ref')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pay_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DUE' => 'danger',
                        'PAID' => 'success',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor.name')
                    ->label('Distributor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->prefix('RM ')
                    ->label('Total')
                    ->numeric(2,'.',',')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')
                    ->label('Product Count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deliver_status')
                    ->label('Shipment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'sent'=> 'warning',
                        'received' => 'success',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockRequests::route('/'),
            'create' => Pages\CreateStockRequest::route('/create'),
            'view' => Pages\ViewStockRequest::route('/{record}'),
            // 'edit' => Pages\EditStockRequest::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Information')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('ref'),
                        Infolists\Components\TextEntry::make('deliver_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'danger',
                                'sent' => 'warning',
                                'received' => 'success',
                                default => 'danger',
                            })
                            ->hintActions([
                                Infolists\Components\Actions\Action::make('sent')
                                    ->label('Mark as Sent')
                                    ->icon('heroicon-s-truck')
                                    ->color('warning')
                                    ->requiresConfirmation()
                                    ->modalHeading('Mark as Sent')
                                    ->modalDescription('Have you sent out for delivery? this action will update the delivery status')
                                    ->modalSubmitActionLabel('Mark Sent')
                                    ->action(function(StockRequest $record){
                                        $record->deliver_status = 'sent';
                                        $record->save();
                                    })
                                    ->hidden(fn(StockRequest $record)=> ($record->pay_status == 'DUE' || $record->deliver_status == 'sent' || $record->deliver_status == 'received')),
                                Infolists\Components\Actions\Action::make('pendingSent')
                                    ->label('Mark as Pending')
                                    ->icon('heroicon-s-truck')
                                    ->color('danger')
                                    ->requiresConfirmation()
                                    ->modalHeading('Mark as Pending')
                                    ->modalDescription('This action will update the delivery status')
                                    ->modalSubmitActionLabel('Mark Pending')
                                    ->action(function(StockRequest $record){
                                        $record->deliver_status = 'pending';
                                        $record->save();
                                    })
                                    ->hidden(fn(StockRequest $record)=> $record->pay_status == 'DUE' || $record->deliver_status == 'pending')
                            ]),
                        Infolists\Components\TextEntry::make('pay_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'DUE' => 'danger',
                                'PAID' => 'success',
                                default => 'danger',
                            })
                            ->hintActions(
                                [
                                    Infolists\Components\Actions\Action::make('paid')
                                        ->label('Mark as PAID')
                                        ->icon('heroicon-m-check')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->modalHeading('Confirm Payment Received')
                                        ->modalDescription('Have you received the payment? this action will update the payment')
                                        ->modalSubmitActionLabel('Confirm PAID')
                                        ->action(function(StockRequest $record){

                                            $distributor = DistributorUser::find($record->distributor_id);

                                            Notification::make()
                                                ->title('Success')
                                                ->body('Marked as paid.')
                                                ->info()
                                                ->color('success')
                                                ->send();

                                            $record->pay_status = 'PAID';
                                            $record->save();

                                            Notification::make()
                                                ->title('Stock Request '.$record->ref.' is marked PAID')
                                                ->color('success')
                                                ->sendToDatabase($distributor);
                                        })
                                        ->hidden(fn(StockRequest $record)=> $record->pay_status === 'PAID'),
                                    Infolists\Components\Actions\Action::make('due')
                                        ->label('Mark as DUE')
                                        ->icon('heroicon-c-x-circle')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->modalHeading('Confirm Payment DUE')
                                        ->modalDescription('This action will update the payment status')
                                        ->modalSubmitActionLabel('Confirm DUE')
                                        ->action(function(StockRequest $record){

                                            Notification::make()
                                                ->title('Updated')
                                                ->body('Marked as DUE.')
                                                ->info()
                                                ->color('info')
                                                ->send();

                                            $record->pay_status = 'DUE';
                                            $record->save();
                                        })
                                        ->hidden(fn(StockRequest $record)=> $record->pay_status === 'DUE')
                                ]
                            ),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->prefix('RM ')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->numeric(2,'.',','),
                    ]),
                Infolists\Components\RepeatableEntry::make('items')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name'),
                                Infolists\Components\TextEntry::make('qty'),
                            ])
                    ])
                    ->columnSpanFull()
            ]);
    }
}
