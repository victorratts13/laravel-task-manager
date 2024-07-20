<?php

namespace App\Filament\Resources\ServiceProccessResource\Pages;

use App\Filament\Resources\ServiceProccessResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceProccesses extends ManageRecords
{
    protected static string $resource = ServiceProccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
