<?php

namespace App\Filament\Widgets;

use App\Models\UserLoginHistory;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Mohammadhprp\IPToCountryFlagColumn\Columns\IPToCountryFlagColumn;

class Loginhistorics extends BaseWidget
{

    protected static ?int $sort = 4 ;
    protected static ?string $heading = 'l\'historique des connexions';
    


    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
        ->query(
            UserLoginHistory::query()->latest()
        )
        ->columns([
            TextColumn::make('user.first_name')->getStateUsing( function (Model $record){
                return $record->user()->first()?->first_name. ' '. $record->user()->first()?->last_name;
             })
            ->label('Nom'),
            IPToCountryFlagColumn::make('ip_address')->location(position: 'above'),
            TextColumn::make('user.email')->label('Email'),
            TextColumn::make('user.telephone')->label('Telephone'),
            TextColumn::make('user.role')->label('Role'),
            TextColumn::make('login_at')->label('date')

        ])->poll('10s');
    }
}
