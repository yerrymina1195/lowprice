<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodeResource\Pages;
use App\Filament\Resources\PaymentMethodeResource\RelationManagers;
use App\Models\PaymentMethode;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentMethodeResource extends Resource
{
    protected static ?string $model = PaymentMethode::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    
    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([

                    TextInput::make('name')->unique(ignoreRecord: true)->required(),
                    TextInput::make('type')->required(),
                    FileUpload::make('image')
                        ->directory('payments')
                        ->image()->preserveFilenames()->previewable(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name'),
                TextColumn::make('type'),
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
            'index' => Pages\ListPaymentMethodes::route('/'),
            'create' => Pages\CreatePaymentMethode::route('/create'),
            'edit' => Pages\EditPaymentMethode::route('/{record}/edit'),
        ];
    }
}
