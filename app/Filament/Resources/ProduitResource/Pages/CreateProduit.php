<?php

namespace App\Filament\Resources\ProduitResource\Pages;

use App\Filament\Resources\ProduitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduit extends CreateRecord
{
    protected static string $resource = ProduitResource::class;



    protected function handleRecordCreation(array $data): Model
    {
        $produit = static::getModel()::create($data);

        
        addImagesToProductfilament($produit->id, $data);


        return $produit;
    }
}
