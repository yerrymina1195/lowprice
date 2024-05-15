<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    
    protected static ?string $navigationGroup = 'Commands';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderidentify')->label('n°')->limit(9),
                TextColumn::make('created_at')->label('date'),
                TextColumn::make('users.first_name') ->getStateUsing( function (Model $record){
                    return $record->users()->first()?->first_name. ' '. $record->users()->first()?->last_name;
                 })->label('Acheteur'),
                 TextColumn::make('prixTotal')->label('montant'),
                 ColumnGroup::make('state', [
                 IconColumn::make('ispaid')
                 ->boolean(),
                 TextColumn::make('statut')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'en cours' => 'gray',
        'terminé' => 'warning',
        'livré' => 'success',
        'annulé' => 'danger',
    })->icon(fn (string $state): string => match ($state) {
        'terminé' => 'heroicon-o-pencil',
        'en cours' => 'heroicon-o-clock',
        'livré' => 'heroicon-o-check-circle',
        'annulé' => 'heroicon-o-check-circle',
    })
]),

ToggleColumn::make('ispaid')->label('')
,
SelectColumn::make('statut')
    ->options([
        'en cours' => 'en cours',
        'terminé' => 'terminé',
        'livré' => 'livré',
        'annulé'=>'annulé'
    ])->selectablePlaceholder(false)->label(''),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            
//             Fieldset::make('produit')
//             ->schema([
                
//                 ImageEntry::make('items.produits.images.image')
//                 ->height(40)
//                 ->circular()
//                 ->stacked()
//                 ->limit(2)
//                 ->limitedRemainingText(),
//     TextEntry::make('prixTotal')->money('XOF'),
    
  
// ])
// ->columns(3),
Section::make('Detail')
    ->description('les details de la commande')
    ->icon('heroicon-m-shopping-bag')->iconColor('primary')
    ->schema([
        RepeatableEntry::make('items')
        ->schema([
        
            ImageEntry::make('produits.premiereImage.image')->height(40)
            ->circular()->label('image'),
        
        TextEntry::make('produits.prix')->money('XOF')->label('prix'),
            TextEntry::make('produits.name')->label('name'),
            TextEntry::make('quantity')->label('quantité'),
            TextEntry::make('subTotal')->label('total')
        ])->label('les produits')
        ->columns(5)->columnSpan(2),

        TextEntry::make('users.first_name') ->getStateUsing( function (Model $record){
            return $record->users()->first()?->first_name. ' '. $record->users()->first()?->last_name;
         })->label('Acheteur'),
         TextEntry::make('users.telephone')->label('Telephone'),
         TextEntry::make('addresses.addresse')->label('Addresse'),
         ImageEntry::make('paymentmethodes.image')
         ->height(40)
         ->circular()->label('Methode de paiement')->tooltip(fn (Model $record): string => "By {$record->paymentmethodes->name}"),
         TextEntry::make('methodelivraisons.name')->getStateUsing( function (Model $record){
            return $record->methodelivraisons()->first()?->name. ': '. $record->methodelivraisons()->first()?->price.' fcfa';
         })->label('Methode de livraison')->weight(FontWeight::Bold),

         TextEntry::make('statut')->badge()
         ->color(fn (string $state): string => match ($state) {
             'en cours' => 'gray',
             'terminé' => 'warning',
             'livré' => 'success',
             'annulé' => 'danger',
         })->icon(fn (string $state): string => match ($state) {
             'terminé' => 'heroicon-o-pencil',
             'en cours' => 'heroicon-o-clock',
             'livré' => 'heroicon-o-check-circle',
             'annulé' => 'heroicon-o-check-circle',
         })->label('statut de la commande')
    ])->columns(2),


    Section::make('Total de la commande')
    ->description('')
    ->aside()
    ->schema([
        TextEntry::make('created_at')->label('Fait le '),
       TextEntry::make('prixTotal')->getStateUsing( function (Model $record){
        return $record->calculerSommePrixPanier().' fcfa';
     })->label('Sous total :'),
       TextEntry::make('methodelivraisons.price')->label('frais livraison'),
       TextEntry::make('methodelivraisons.price')->getStateUsing( function (Model $record){
        return $record->methodelivraisons()->first()?->price + $record->calculerSommePrixPanier().' fcfa';
     })->label(' Total à payer'),
    ])






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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
