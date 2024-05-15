<?php

namespace App\Filament\Resources\PaymentMethodeResource\Pages;

use App\Filament\Resources\PaymentMethodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethodes extends ListRecords
{
    protected static string $resource = PaymentMethodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
