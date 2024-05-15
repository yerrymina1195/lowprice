<?php

namespace App\Filament\Resources\PackResource\Pages;

use App\Filament\Resources\PackResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
class CreatePack extends CreateRecord
{
    protected static string $resource = PackResource::class;



    protected function handleRecordCreation(array $data): Model
    {
        $pack = static::getModel()::create($data);

        
        addImagesToPackfilament($pack->id, $data);


        return $pack;
    }
}
