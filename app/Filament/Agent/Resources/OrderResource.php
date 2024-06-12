<?php

namespace App\Filament\Agent\Resources;

use App\Filament\Agent\Resources\OrderResource\Pages;
use App\Filament\Agent\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-m-bolt';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $agentID = $user->id;
        
        return parent::getEloquentQuery()->where('agent_id', $agentID);
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    private static function getAvailableProduct()
    {
        return Product::where('status', '1')->get()->pluck('name','id');
    }

    public static function form(Form $form): Form
    {
        // $prod = OrderResource::getAvailableProduct();
        // dd($prod);
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_name')
                    ->required(),
                Forms\Components\TextInput::make('customer_phone')
                    ->required()
                    ->regex('/^01\d{8,}$/')
                    ->helperText('start with 01XXXXXXXX .must not contain - or space'),
                Forms\Components\TextInput::make('customer_email')
                    ->required()
                    ->email(),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('agent_id')
                    ->default(fn()=>auth()->id()),
                Forms\Components\Hidden::make('distributor_id')
                    ->default(fn()=> auth()->user()->distributor_id),
                Forms\Components\TextInput::make('sub_total')
                    ->prefix('RM')
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('grand_total')
                    ->prefix('RM')
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('agent_comm')
                    ->prefix('RM')
                    ->label('Agent Commission')
                    ->readOnly()
                    ->required(),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->live(true)
                    ->afterStateUpdated(fn(Set $set, Get $get)=> self::calculateTotals($get, $set))
                    ->grid(3)
                    ->columnSpanFull()
                    ->required()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product Name')
                            ->options(OrderResource::getAvailableProduct())
                            ->live(onBlur:true),
                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->live(onBlur:true)
                            ->afterStateUpdated(function(Get $get, Set $set){
                                $pid = $get('product_id');
                                $price = Product::find($pid)->price;
                                $amount = $get('qty') * $price;
                                $set('amount', $amount);
                            }),
                        Forms\Components\TextInput::make('amount')
                            ->prefix('RM')
                            ->readOnly()
                    ])
            ]);
    }

    public static function calculateTotals(Get $get, Set $set): void
    {
        $items = $get('items');
        // Log::debug('items ', ['deb'=>$items]);
        $subTotal = 0;
        $grandTotal = 0;
        $delivery = Setting::where('skey', 'DELIVERY_PRICE')->value('sval');
        $baseComm = auth()->user()->comm_amount;
        $totalComm = 0;

        foreach($items as $i):
            $pid = $i['product_id'];
            $qty = $i['qty'];
            if($pid):
                $product = Product::find($pid);
                $total = $qty * $product->price;
                $subTotal = $subTotal + $total;
                $comm = $baseComm * $qty;
                $totalComm = $totalComm + $comm;
            endif;
        endforeach;

        $grandTotal = $subTotal + $delivery;

        $set('sub_total', $subTotal);
        $set('grand_total', $grandTotal);
        $set('agent_comm', $totalComm);
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
                Tables\Columns\TextColumn::make('agent_comm')
                    ->prefix('RM ')
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
                //
            ])
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
            RelationManagers\ItemsRelationManager::class
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
