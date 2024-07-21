<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackEndController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class updater extends Command
{
    public static string $repository = "victorratts13/laravel-task-manager";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:updater {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the system based on the github repository';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $backend = new BackEndController();
        $this->alert("Updater 1.0.0");
        $version = $this->argument('version');
        $this->warn("| loading...");
        if (!isset($version)) {
            $metadata = BackEndController::GetLastVersion($this::$repository);
        } else {
            $metadata = BackEndController::GetVersion($this::$repository, $version);
        }

        if (!isset($metadata)) {
            $this->error("| Fail to get this version, please, verify your repository or version-tag!");
            return;
        }

        $this->warn("| Get version {$metadata->name}");
        $this->info("| Download from: {$metadata->zipball_url}");
        $result = BackEndController::downloadAndUpdate($metadata->zipball_url);

        if($result->status){
            $this->info("| {$result->message}");
        } else {
            $this->error("| {$result->message}");
            return ;
        }

        $this::Posinstall();
        
    }

    protected static function Posinstall() {
        Artisan::call('migrate');
        Artisan::call('optimize:clear');
        Process::fromShellCommandline("composer install");
    }
}
