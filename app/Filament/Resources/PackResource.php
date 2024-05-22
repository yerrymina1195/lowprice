<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackResource\Pages;
use App\Filament\Resources\PackResource\RelationManagers;
use App\Models\Pack;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackResource extends Resource
{
    protected static ?string $model = Pack::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    
    protected static ?string $navigationGroup = 'Product';
    

    protected static ?int $navigationSort = 6;

 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->unique(ignoreRecord: true)
                        ->required()->minLength(3)->maxLength(250),
                        TextInput::make('prix')->integer()
                        ->required(),
    
                    MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->required(),
                        
    
                ])->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make()->schema([
                        FileUpload::make('image')
                            ->directory('packs')->multiple()
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
                ImageColumn::make('images.image'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('description')
                ->limit(40)->label('Description'),
                ToggleColumn::make('online'),
                TextColumn::make('prix'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPacks::route('/'),
            'create' => Pages\CreatePack::route('/create'),
            'edit' => Pages\EditPack::route('/{record}/edit'),
        ];
    }
}
