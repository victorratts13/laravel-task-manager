<?php

namespace App\Console\Commands;

use App\Http\Controllers\UtilsController;
use Illuminate\Console\Command;

class MakeRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-release {version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create release from project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');
        $this->alert("Make Release 1.0.0");
        $this->info("| Making zip file...");
        $result = UtilsController::MakeZipFile("{$version}.zip");
        $this->info("| Ziped file: {$result}");

    }
}
