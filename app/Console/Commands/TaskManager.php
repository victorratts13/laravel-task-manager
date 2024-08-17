<?php

namespace App\Console\Commands;

use App\Jobs\ComandExecJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

class TaskManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task-manager {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'runs the task and process queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->option('id');

        $users = User::all();
        $processess = [];

        $this->alert("Laravel TaskManager Console");
        
        while (true) {
            // Process::fromShellCommandline("clear");
            $tasklist = Cache::get('task-manager-provider');

            if(!isset($tasklist)){
                $output = [];
                $command = "ps aux | grep -v grep";
                exec($command, $output);
                Cache::put('task-manager-provider', $output, 10);
            }

            $this->info('| Checking users');
            foreach ($users as $user) {
                $this->warn("|- {$user->name}");
                $this->info(' |- Checking Enviroments');
                $enviroments = $user->enviroments()->get();
                foreach ($enviroments as $envs) {
                    $this->warn(" |- {$envs->name}");
                    $this->info('  |- Checking Services');
                    if($envs->status){
                        $services = $envs->services()->get();
                        foreach ($services as $service) {
                            $this->warn("  |- Command: {$service->command}");
                            $this->info('   |- Checking time execution...');
                            if($service->status){
                                if (time() > (strtotime($service->last_execution) + $service->interval)) {
                                    $this->info('    |- Queued command');
                                    ComandExecJob::dispatch($service)->onQueue($envs->queue()->first()->name);
                                    $service->update(['last_execution' => now()]);
                                } else {
                                    $this->warn('    |- No time execution');
                                }
                            }
                        }
                    }
                }
            }
            sleep(1);
        }
    }
}
