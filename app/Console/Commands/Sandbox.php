<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackEndController;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backend = new BackEndController();
        dd($backend->loadEnvironmentFromString("FOO=BAR"));
    }
}
