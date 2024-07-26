<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

class Monitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-c-tv';

    protected static string $view = 'filament.pages.monitor';

    public array $psaux = [];

    protected function getHeaderWidgets(): array
    {
        // $this->dispatch('page-monitor');
        return [];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    private function convertTimeToSeconds($time)
    {
        $parts = explode(':', $time);
        $seconds = 0;
        if (count($parts) == 3) {
            $seconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        } elseif (count($parts) == 2) {
            $seconds = ($parts[0] * 60) + $parts[1];
        }
        return $seconds;
    }

    public function monitor()
    {
        // Executa o comando ps com os parâmetros para obter as informações desejadas
        $output = [];
        $command = "ps aux | grep -v grep";
        exec($command, $output);

        // Processa a saída do comando para extrair as informações necessárias
        $processes = [];
        foreach ($output as $line) {
            $columns = preg_split('/\s+/', $line);

            if (count($columns) >= 11) {
                $processes[] = (object)[
                    'pid' => $columns[1],
                    'user' => $columns[0],
                    'cpu' => $columns[2],
                    'mem' => $columns[3],
                    'command' => array_reverse(explode('/', $columns[10]))[0],
                    'time' => $this->convertTimeToSeconds($columns[9]),
                ];
            }
        }

        return collect($processes);
    }

    #[On('load-monitor')]
    public function monitorProcessor()
    {
        $monitorContent =  $this->monitor();
        $this->psaux = $monitorContent->values()->toArray();
    }

    public function render(): View
    {
        $this->dispatch('load-monitor');
        return view(static::$view)
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }
}
