<?php

namespace App\Filament\Distributor\Resources;

use App\Filament\Distributor\Resources\AgentUserResource\Pages;
use App\Filament\Distributor\Resources\AgentUserResource\RelationManagers;
use App\Infolists\Components\AgentPaidCommission;
use App\Infolists\Components\AgentUnpaidCommission;
use App\Models\AgentUser;
use App\Services\CommissionService;
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
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;

class AgentUserResource extends Resource
{
    protected static ?string $model = AgentUser::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    public static function canCreate(): bool
    {
        return true;
    }

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
                    ->required()
                    ->readOnly(fn (string $context): bool => $context === 'edit'),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->regex('/^01\d{8,}$/')
                    ->helperText('start with 01XXXXXXXX .must not contain - or space')
                    ->readOnly(fn (string $context): bool => $context === 'edit'),
                Forms\Components\TextInput::make('comm_amount')
                    ->label('Commission Amount')
                    ->default(10)
                    ->prefix('RM')
                    ->readOnly()
                    ->hiddenOn('edit'),
                Forms\Components\TextInput::make('password')
                    ->hiddenOn('edit')
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
                    ->default(fn()=> auth()->guard('distributor')->id()),
                Forms\Components\TextInput::make('bank_name')
                    ->required(),
                Forms\Components\TextInput::make('bank_acc_no')
                    ->required(),
                Forms\Components\TextInput::make('bank_acc_name')
                    ->required()
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
            RelationManagers\PaidCommissionsRelationManager::class
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Agent Details')
                    ->columns(3)
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('disableOrder')
                            ->label('Disable Ordering')
                            ->icon('heroicon-s-stop')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalDescription('Customer will not be able to place an order until re-enabled.')
                            ->hidden(fn(AgentUser $record)=> $record->enable_order == 'NO')
                            ->action(function(AgentUser $record){
                                $record->enable_order = 'NO';
                                $record->save();

                                Notification::make()
                                    ->title('Order Stopped')
                                    ->body('No customer can order from this agent.')
                                    ->info()
                                    ->color('info')
                                    ->send();
                            }),
                        Infolists\Components\Actions\Action::make('enableOrder')
                            ->label('Enable Ordering')
                            ->icon('heroicon-s-play')
                            ->color('success')
                            ->requiresConfirmation()
                            ->modalDescription('Customer will be able to place an order.')
                            ->hidden(fn(AgentUser $record)=> $record->enable_order == 'YES')
                            ->action(function(AgentUser $record){
                                $record->enable_order = 'YES';
                                $record->save();

                                Notification::make()
                                    ->title('Order Started')
                                    ->body('Customer can now order from this agent.')
                                    ->success()
                                    ->color('success')
                                    ->send();
                            }),
                        Infolists\Components\Actions\Action::make('banAgent')
                            ->label('Ban Agent')
                            ->icon('heroicon-s-hand-raised')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->hidden(fn(AgentUser $record)=> $record->status == '0')
                            ->modalDescription('Agent will not be able to login.')
                            ->action(function(AgentUser $record){
                                $record->status = 0;
                                $record->save();

                                Notification::make()
                                    ->title('Agent Banned')
                                    ->body('Agent no longer has access to dashboard.')
                                    ->info()
                                    ->color('info')
                                    ->send();
                            }),
                        Infolists\Components\Actions\Action::make('unbanAgent')
                            ->label('Unban Agent')
                            ->icon('heroicon-c-hand-thumb-up')
                            ->color('success')
                            ->requiresConfirmation()
                            ->hidden(fn(AgentUser $record)=> $record->status == '1')
                            ->modalDescription('Agent will be able to login.')
                            ->action(function(AgentUser $record){
                                $record->status = 1;
                                $record->save();

                                Notification::make()
                                    ->title('Agent Unbanned')
                                    ->body('Agent now has access to dashboard.')
                                    ->success()
                                    ->color('success')
                                    ->send();
                            })
                    ])
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('phone'),
                        Infolists\Components\TextEntry::make('bank_name'),
                        Infolists\Components\TextEntry::make('bank_acc_no'),
                        Infolists\Components\TextEntry::make('bank_acc_name'),
                    ]),
                Infolists\Components\Section::make('Commissions Data')
                    ->columns(2)
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('paid')
                            ->label('Set PAID')
                            ->color('success')
                            ->requiresConfirmation()
                            ->icon('heroicon-s-currency-dollar')
                            ->modalDescription('Unpaid amount will be marked as paid. confirm?')
                            ->action(function(AgentUser $record){
                                $comService = new CommissionService;
                                $comService->markUnpaidCommission($record->id);

                            })
                    ])
                    ->schema([
                        AgentUnpaidCommission::make('id')
                            ->label('Unpaid'),
                        AgentPaidCommission::make('id')
                            ->label('Paid')
                    ])
            ]);
    }

}
