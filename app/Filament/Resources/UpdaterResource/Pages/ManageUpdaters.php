<?php

namespace App\Filament\Resources\UpdaterResource\Pages;

use App\Filament\Resources\UpdaterResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ManageUpdaters extends ManageRecords
{
    protected static string $resource = UpdaterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit-env')
                ->requiresConfirmation()
                ->label('Variáveis de conf.')
                ->icon('heroicon-c-cog-6-tooth')
                ->fillForm(function () {
                    return [
                        'env' => File::get(base_path('.env'))
                    ];
                })
                ->form([
                    Toggle::make('migrate')->label('Executar migração de banco de dados após a edição do arquivo'),
                    Textarea::make('env')
                        ->label('Edite as variaveis de ambiente do sistema')
                        ->rows(20)
                ])
                ->action(function ($data) {
                    File::put(base_path('.env'), $data['env']);
                    if ($data['migrate']) {
                        Artisan::call('migrate');
                    }

                    Notification::make()
                        ->success()
                        ->title('Sucesso')
                        ->body('Variáveis de ambiete atualizadas com sucesso!!')
                        ->send();
                }),

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
