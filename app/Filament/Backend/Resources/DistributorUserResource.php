<?php

namespace App\Filament\Backend\Resources;

use App\Filament\Backend\Resources\DistributorUserResource\Pages;
use App\Filament\Backend\Resources\DistributorUserResource\RelationManagers;
use App\Models\DistributorUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistributorUserResource extends Resource
{
    protected static ?string $model = DistributorUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->regex('/^01\d{8,}$/'),
                Forms\Components\TextInput::make('agent_commission')
                    ->label('Agent Base Commission')
                    ->default(10)
                    ->prefix('RM'),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password(),
                Forms\Components\Select::make('status')
                    ->default(1)
                    ->options([
                        '0'=>'Inactive',
                        '1'=>'Active'
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'danger',
                    })
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
            ])
            ->defaultSort('created_at','DESC');
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
            'index' => Pages\ListDistributorUsers::route('/'),
            'create' => Pages\CreateDistributorUser::route('/create'),
            'edit' => Pages\EditDistributorUser::route('/{record}/edit'),
        ];
    }
}
