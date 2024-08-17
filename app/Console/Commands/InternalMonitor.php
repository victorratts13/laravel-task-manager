<?php

namespace App\Console\Commands;

use App\Http\Controllers\TaskManagerController;
use Illuminate\Console\Command;

class InternalMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:internal-monitor {process?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Intermal monitor of process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $command = $this->argument('process');
        $taskManager = new TaskManagerController();
        // dd($command);
        // dd($taskManager->psaux()->where('command', 'watch ps aux'));
        $this->alert("Internal Monitor");
        if (isset($command)) {
            $result = $taskManager
                ->psaux()
                // ->where('command', $command)
                ->filter(function($mp) use ($command){
                    return strpos($mp->command, $command);
                    return false;
                })
                ->map(function ($mp) {
                    return [
                        $mp->id,
                        $mp->pid,
                        $mp->user,
                        $mp->cpu,
                        $mp->mem,
                        $mp->command
                    ];
                })
                ->values();

            $this->table([
                '#ID',
                'PID',
                'USER',
                'CPU',
                'MEM',
                'COMMAND'
            ], $result);
        } else {
            $result = $taskManager
                ->psaux()
                ->map(function ($mp) {
                    return [
                        $mp->id,
                        $mp->pid,
                        $mp->user,
                        $mp->cpu,
                        $mp->mem,
                        substr($mp->command, 0, 32) . '...' . substr($mp->command, (strlen($mp->command) - 32), (strlen($mp->command) + 32))
                    ];
                })
                ->values();

            $this->table([
                '#ID',
                'PID',
                'USER',
                'CPU',
                'MEM',
                'COMMAND'
            ], $result);
        }
    }
}