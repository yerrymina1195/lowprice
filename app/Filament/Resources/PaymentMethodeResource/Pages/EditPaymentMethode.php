<?php

namespace App\Filament\Resources\PaymentMethodeResource\Pages;

use App\Filament\Resources\PaymentMethodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethode extends EditRecord
{
    protected static string $resource = PaymentMethodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
