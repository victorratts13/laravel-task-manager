<?php

namespace App\Filament\Resources\EnviromentResource\Pages;

use App\Filament\Resources\EnviromentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEnviroments extends ManageRecords
{
    protected static string $resource = EnviromentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
