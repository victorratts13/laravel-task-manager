<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
        
        if(!file_exists(__DIR__ . "/../../../.env")){
            $this->warn("| Create new .env file");
            copy(__DIR__ . "/../../../.env.example", __DIR__ . "/../../../.env");
        } else {
            $this->info("|✅ Env file is created");
        } 

        $key = env("APP_KEY");

        if(!is_dir(__DIR__ . "/../../../vendor")){
            $this->warn("| Create new install");
            exec("{$composer} install");
            if(!isset($key)){
                $this->warn("| Create new key");
                exec("php artisan key:generate");
            }
        } else {
            if(!isset($key)){
                $this->warn("| Create new key");
                exec("php artisan key:generate");
            }
            $this->info("|✅ vendor folder is created");
        }

        if(!static::CheckDatabase()){
            $this->warn("| migrate database");
            Artisan::call("migrate");
        } else {
            $this->info("|✅ Database is migrated");
        }

    }

    private static function CheckDatabase() {
        try {
            DB::table("updaters")->first();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
