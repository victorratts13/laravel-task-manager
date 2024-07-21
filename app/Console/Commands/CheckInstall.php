<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check installations and services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->alert("Checking instalation service");
        $composer = env('COMPOSER_ALIASE', 'composer');

        if(file_exists(__DIR__ . "/../../../.env")){
            $this->warn("| Create new .env file");
            copy(__DIR__ . "/../../../.env.example", __DIR__ . "/../../../.env");
        } else {
            $this->info("|✅ Env file is created");
        } 

        if(!is_dir(__DIR__ . "/../../../vendor")){
            $key = env("APP_KEY");
            $this->warn("| Create new install");
            exec("{$composer} install");
            if(isset($key)){
                $this->warn("| Create new key");
                exec("php artisan key:generate");
            }
        } else {
            $this->info("|✅ vendor folder is created");
        }


    }
}
