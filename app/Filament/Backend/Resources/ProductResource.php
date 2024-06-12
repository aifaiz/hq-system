<?php

namespace App\Filament\Backend\Resources;

use App\Filament\Backend\Resources\ProductResource\Pages;
use App\Filament\Backend\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
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
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('RM'),
                                Forms\Components\TextArea::make('description')
                                    ->columnSpanFull()
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
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Default Price')
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
