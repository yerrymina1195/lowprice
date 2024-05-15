<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LivraisonResource\Pages;
use App\Filament\Resources\LivraisonResource\RelationManagers;
use App\Models\Livraison;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LivraisonResource extends Resource
{
    protected static ?string $model = Livraison::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Section::make()->schema([

                    TextInput::make('name')->required(),
                    TextInput::make('type')->required(),
                TextInput::make('price')->integer()->required(),
                Toggle::make('ispublished')
                ->label('ispublished')
                ->onColor('success')
                ->offColor('danger')
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
             TextColumn::make('name'),
             TextColumn::make('type'),
             TextColumn::make('price'),
             ToggleColumn::make('ispublished')
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
            'index' => Pages\ListLivraisons::route('/'),
            'create' => Pages\CreateLivraison::route('/create'),
            'edit' => Pages\EditLivraison::route('/{record}/edit'),
        ];
    }
}
