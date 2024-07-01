<?php

namespace App\Filament\Distributor\Resources;

use App\Filament\Distributor\Resources\StockRequestResource\Pages;
use App\Filament\Distributor\Resources\StockRequestResource\RelationManagers;
use App\Models\DistributorProductQty;
use App\Models\DistributorUser;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\User;
use App\Services\StockService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications;
use Filament\Notifications\Notification;

class StockRequestResource extends Resource
{
    protected static ?string $model = StockRequest::class;

    protected static ?string $navigationIcon = 'heroicon-c-tag';

    public static function getNavigationBadge(): ?string
    {
        $distributorID = auth()->id();
        return static::getModel()::where('deliver_status', 'sent')
            ->where('pay_status', 'PAID')
            ->where('distributor_id', $distributorID)->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Stock Sent';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'warning' : 'primary';
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('distributor_id')
                    ->default(auth()->id())
                    ->required(),
                Forms\Components\Hidden::make('ref')
                    ->default(function(){
                        $stockService = new StockService;
                        return $stockService->generateRequestStockRef();
                    })
                    ->required(),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('sub_total')
                            ->prefix('RM')
                            ->readOnly(),
                        Forms\Components\TextInput::make('delivery_price')
                            ->prefix('RM')
                            ->readOnly(),
                        Forms\Components\TextInput::make('total_amount')
                            ->prefix('RM')
                            ->readOnly(),
                    ]),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->required()
                    ->columnSpanFull()
                    ->live(onBlur: true)
                    ->defaultItems(0)
                    ->addActionLabel('Add more items')
                    ->itemLabel(function(array $state){
                        if($state['product_id']){
                            $product = Product::find($state['product_id']);
                            return $product->name .' ('.$state['qty'].')';
                        }
                        return null;
                    })
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Choose Product')
                                    ->options(Product::where('status', '1')->get()->pluck('name','id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Get $get,Set $set)=>self::calculateTotals($get,$set)),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Qty Stock')
                                    ->numeric()
                                    ->required()
                                    ->default(10)
                                    ->minValue(10)
                            ])
                    ])
                    ->afterStateUpdated(fn(Get $get,Set $set)=>self::calculateTotals($get,$set))
            ]);
    }

    public static function calculateTotals($get, $set)
    {
        $items = $get('items');
        $subTotal = 0;
        $grandTotal = 0;
        $deliveryPrice = 0;
        if($items):
            $deliveryPrice = Setting::where('skey', 'DELIVERY_PRICE')->value('sval');
            foreach($items as $item):
                if($item['product_id']):
                    $product = Product::find($item['product_id']);
                    $total = $product->distributor_price * $item['qty'];
                    $subTotal = $subTotal + $total;
                endif;
            endforeach;
        endif;

        $grandTotal = $subTotal + $deliveryPrice;
        $set('delivery_price', $deliveryPrice);
        $set('sub_total', $subTotal);
        $set('total_amount', $grandTotal);
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
                Tables\Actions\Action::make('received')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Stock Received')
                    ->modalDescription('Have you received the stock? this action will update your inventory')
                    ->modalSubmitActionLabel('Confirm Received')
                    ->action(function(StockRequest $record){
                        $requestID = $record->id;
                        $distributorID = $record->distributor_id;
                        self::stockReceived($requestID, $distributorID);

                        Notification::make()
                            ->title('Success')
                            ->body('Your inventory has been updated.')
                            ->info()
                            ->color('success')
                            ->send();

                        $record->deliver_status = 'received';
                        $record->save();

                        $distributor = DistributorUser::find($distributorID);
                        $admins = User::all();

                        Notification::make()
                            ->title('Stock has been received')
                            ->body($distributor->name .' has received the stock '. $record->ref)
                            ->success()
                            ->color('success')
                            ->actions([
                                Notifications\Actions\Action::make('view')
                                    ->url('/backend/stock-requests/'.$record->id)
                            ])
                            ->sendToDatabase($admins);
                    })
                    ->hidden(fn(StockRequest $record)=> $record->deliver_status === 'received'),
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
            'view' => Pages\ViewStockRequest::route('/{record}/view'),
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
                            ->hintActions(
                                [
                                    Infolists\Components\Actions\Action::make('received')
                                        ->label('Mark as received')
                                        ->icon('heroicon-m-check')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->modalHeading('Confirm Stock Received')
                                        ->modalDescription('Have you received the stock? this action will update your inventory')
                                        ->modalSubmitActionLabel('Confirm Received')
                                        ->action(function(StockRequest $record){
                                            $requestID = $record->id;
                                            $distributorID = $record->distributor_id;
                                            $distributor = DistributorUser::find($distributorID);
                                            self::stockReceived($requestID, $distributorID);

                                            Notification::make()
                                                ->title('Success')
                                                ->body('Your inventory has been updated.')
                                                ->info()
                                                ->color('success')
                                                ->send();

                                            $record->deliver_status = 'received';
                                            $record->save();

                                            $admins = User::all();

                                            Notification::make()
                                                ->title('Stock has been received')
                                                ->body($distributor->name .' has received the stock '. $record->ref)
                                                ->success()
                                                ->color('success')
                                                ->actions([
                                                    Notifications\Actions\Action::make('view')
                                                        ->url('/backend/stock-requests/'.$record->id)
                                                ])
                                                ->sendToDatabase($admins);
                                        })
                                        ->hidden(fn(StockRequest $record)=> $record->deliver_status === 'received')
                                ]
                            ),
                        Infolists\Components\TextEntry::make('pay_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'DUE' => 'danger',
                                'PAID' => 'success',
                                default => 'danger',
                            }),
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

    public static function stockReceived($requestID, $distributorID)
    {
        $requestedStocks = StockRequestItem::where('stock_request_id', $requestID)->get();
        foreach($requestedStocks as $rs):
            $requestedQty = $rs->qty;
            $stock = DistributorProductQty::where('distributor_id', $distributorID)->where('product_id', $rs->product_id)->first();
            $oldStock = $stock->qty;
            $newStock = $oldStock + $requestedQty;
            $stock->qty = $newStock;
            $stock->save();
        endforeach;
    }
}
