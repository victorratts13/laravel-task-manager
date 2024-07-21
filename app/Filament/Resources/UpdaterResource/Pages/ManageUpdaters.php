<?php

namespace App\Filament\Resources\UpdaterResource\Pages;

use App\Filament\Resources\UpdaterResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Artisan;

class ManageUpdaters extends ManageRecords
{
    protected static string $resource = UpdaterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Update')
                ->icon('heroicon-c-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call("app:updater");
                    Notification::make()
                        ->title('System updated')
                        ->success()
                        ->body('Your system is updated with success.')
                        ->send();
                })
        ];
    }
}
