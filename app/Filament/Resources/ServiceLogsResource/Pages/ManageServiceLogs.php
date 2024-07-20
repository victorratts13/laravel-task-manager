<?php

namespace App\Filament\Resources\ServiceLogsResource\Pages;

use App\Filament\Resources\ServiceLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceLogs extends ManageRecords
{
    protected static string $resource = ServiceLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
