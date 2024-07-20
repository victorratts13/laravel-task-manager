<?php

namespace App\Filament\Resources\QueuesResource\Pages;

use App\Filament\Resources\QueuesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageQueues extends ManageRecords
{
    protected static string $resource = QueuesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
