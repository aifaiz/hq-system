<?php

namespace App\Filament\Distributor\Resources;

use App\Filament\Distributor\Resources\AgentUserResource\Pages;
use App\Filament\Distributor\Resources\AgentUserResource\RelationManagers;
use App\Models\AgentUser;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgentUserResource extends Resource
{
    protected static ?string $model = AgentUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $distributorID = $user->id;
        $builder = parent::getEloquentQuery();

        $builder = $builder->where('distributor_id', $distributorID);

        return $builder;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->afterStateUpdated(function($state, Set $set){
                        $slug = Str::slug($state);
                        $check = AgentUser::where('refcode', $slug)->first();
                        if($check):
                            $slug = $slug.'-'.rand(99,9999);
                        endif;
                        $set('refcode', $slug);
                    })
                    ->live(onBlur:true),
                Forms\Components\TextInput::make('refcode')
                    ->required()
                    ->readOnly()
                    ->helperText('Auto generated'),
                Forms\Components\TextInput::make('email')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->regex('/^01\d{8,}$/')
                    ->helperText('start with 01XXXXXXXX .must not contain - or space'),
                Forms\Components\TextInput::make('comm_amount')
                    ->label('Commission Amount')
                    ->default(10)
                    ->prefix('RM'),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password(),
                Forms\Components\Select::make('status')
                    ->default(1)
                    ->options([
                        '0'=>'Disabled',
                        '1'=>'Enabled'
                    ])
                    ->helperText('Allow/Disallow agent to login'),
                Forms\Components\TextInput::make('lp_expire')
                    ->label('Landing page expiry')
                    ->readOnly()
                    ->default(function(){
                        $now = Carbon::today();
                        $nextYear = $now->addYear();
                        return $nextYear->format('Y-m-d');
                    }),
                Forms\Components\Hidden::make('distributor_id')
                    ->default(fn()=> auth()->guard('distributor')->id())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('comm_amount')
                    ->label('Base Comm.')
                    ->sortable()
                    ->searchable()
                    ->prefix('RM '),
                Tables\Columns\IconColumn::make('status')
                    ->sortable()
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
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAgentUsers::route('/'),
            'create' => Pages\CreateAgentUser::route('/create'),
            'edit' => Pages\EditAgentUser::route('/{record}/edit'),
            'view' => Pages\ViewAgent::route('{record}/view')
        ];
    }
}
