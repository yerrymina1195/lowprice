<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduitImageResource\Pages;
use App\Filament\Resources\ProduitImageResource\RelationManagers;
use App\Models\ProduitImage;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProduitImageResource extends Resource
{
    protected static ?string $model = ProduitImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Product';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                              Section::make()->schema([

                    FileUpload::make('image')
                        ->directory('produits')
                        ->image()->preserveFilenames()->previewable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('produits.name')->label('produit')->searchable(),
                TextColumn::make('produits.categories.name')->label('categorie')->searchable()            ])
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
            'index' => Pages\ListProduitImages::route('/'),
            'create' => Pages\CreateProduitImage::route('/create'),
            'edit' => Pages\EditProduitImage::route('/{record}/edit'),
        ];
    }
}
