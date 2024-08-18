<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackEndController;
use App\Http\Controllers\TaskManagerController;
use Illuminate\Console\Command;

class Sandbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sandbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests enviroment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       dd((new TaskManagerController())->ExecPid("watch ps aux"));
    }
}
