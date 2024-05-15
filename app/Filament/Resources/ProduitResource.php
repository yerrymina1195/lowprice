<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduitResource\Pages;
use App\Filament\Resources\ProduitResource\RelationManagers;
use App\Models\Produit;
use App\Models\SubCategorie;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ProduitResource extends Resource
{
    protected static ?string $model = Produit::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->required()->minLength(3)->maxLength(250),
                    Select::make('categorie_id')->relationship(name: 'categories', titleAttribute: 'name')->native(false)->preload()->live()->required(),
                    Select::make('sub_categorie_id')->options(fn (Get $get): array => SubCategorie::where('categorie_id', $get('categorie_id'))->pluck('name', 'id')->toArray())->preload(),
                    TextInput::make('prix')->integer()
                    ->required(),
                    TextInput::make('quantity')->integer()
                    ->required(),

                MarkdownEditor::make('description')
                    ->columnSpanFull()
                    ->required(),
                    

            ])->columnSpan(2)->columns(2),
            Group::make()->schema([
                Section::make()->schema([
                    FileUpload::make('image')
                        ->directory('produits')->multiple()
                        ->image()->preserveFilenames()->hiddenOn('edit'),
                ])->columnSpan(1),

                Toggle::make('statut')
                    ->label('online')
                    ->onColor('success')
                    ->offColor('danger')
            ])


        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.image')->circular()
                ->stacked()
                ->limit(2)
                ->limitedRemainingText(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('description')
                ->limit(70)->label('Description'),
                ToggleColumn::make('statut')->label('online'),
                TextColumn::make('prix'),
                ToggleColumn::make('nouveaute'),
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
            RelationManagers\ImagesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduits::route('/'),
            'create' => Pages\CreateProduit::route('/create'),
            'edit' => Pages\EditProduit::route('/{record}/edit'),
        ];
    }
}
