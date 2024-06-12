<?php

namespace App\Filament\Distributor\Resources;

use App\Filament\Distributor\Resources\CommissionPayResource\Pages;
use App\Filament\Distributor\Resources\CommissionPayResource\RelationManagers;
use App\Models\AgentUser;
use App\Models\CommissionPay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommissionPayResource extends Resource
{
    protected static ?string $model = CommissionPay::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    protected static ?string $navigationLabel = 'Paid Commissions';

    protected static ?string $modelLabel = 'Paid Commissions';

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
                // to create record in paid commissions. view agent individually
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Process')
                    ->date('d/m/Y g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ref')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Orders Involved')
                    ->counts('items')
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListCommissionPays::route('/'),
            'create' => Pages\CreateCommissionPay::route('/create'),
            'edit' => Pages\EditCommissionPay::route('/{record}/edit'),
        ];
    }
}
