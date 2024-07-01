<?php

namespace App\Filament\Backend\Resources;

use App\Filament\Backend\Resources\ProductResource\Pages;
use App\Filament\Backend\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-gift';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Product Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Product Name')
                                    ->required()
                                    ->columnSpanFull()
                                    ->live(onBlur:true)
                                    ->afterStateUpdated(function($state, Set $set){
                                        $slug = \Illuminate\Support\Str::slug($state);
                                        $set('slug', $slug);
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Product URL (auto generated)')
                                    ->readOnly()
                                    ->prefix('AGENT_LINK')
                                    ->required()
                                    ->unique()
                                    ->columnSpanFull()
                                    ->helperText(function($state){
                                        $slug = $state ?? 'product-name';
                                        return 'eg: '. route('agent.product.view', ['refcode'=>'test','slug'=>$slug]);
                                    })
                                    ->live(),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('RM'),
                                        Forms\Components\TextInput::make('distributor_price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('RM'),
                                        Forms\Components\TextInput::make('agent_comm')
                                            ->required()
                                            ->numeric()
                                            ->prefix('RM'),
                                    ]),
                                Forms\Components\Textarea::make('short_desc')
                                    ->label('Short Description')
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->required()
                            ])
                            ->columnSpan(2),
                        Forms\Components\Section::make('Actions')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        '0'=>'Inactive',
                                        '1'=>'Active'
                                    ])
                                    ->required()
                                    ->default(1),
                                Forms\Components\Repeater::make('images')
                                    ->relationship()
                                    ->orderColumn('priority')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->directory('products')
                                            ->image()
                                            ->required()
                                    ])
                            ])
                            ->columnSpan(1)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->circular()
                    ->size(60,60),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Retail Price')
                    ->prefix('RM')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('distributor_price')
                    ->label('Distributor Price')
                    ->prefix('RM')
                    ->sortable()
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
