<?php

namespace App\Filament\Resources\ProduitImageResource\Pages;

use App\Filament\Resources\ProduitImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduitImage extends EditRecord
{
    protected static string $resource = ProduitImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
