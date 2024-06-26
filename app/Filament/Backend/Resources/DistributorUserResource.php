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
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Filament\Tables\View\TablesRenderHook;
use Filament\Actions\Action;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class DistributorUserResource extends Resource
{
    protected static ?string $model = DistributorUser::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Distributors';

    protected static ?string $modelLabel = 'Distributors';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function($state, Set $set){
                        $slug = Str::slug($state);
                        $set('refcode', $slug);
                    }),
                Forms\Components\TextInput::make('refcode')
                    ->label('Refferral Code')
                    ->required()
                    ->regex('/^[a-zA-Z0-9-]+$/')
                    ->unique()
                    ->helperText('code used to register agent. Lowercase, no space, "-" allowed'),
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
                    ->password()
                    ->hiddenOn('edit'),
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
        // add extra button beside search input
        // FilamentView::registerRenderHook(
        //     TablesRenderHook::TOOLBAR_SEARCH_AFTER,
        //     fn (): View => view('testing'),
        // );

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agents_count')
                    ->counts('agents')
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
