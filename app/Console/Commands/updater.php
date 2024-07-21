<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackEndController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class updater extends Command
{
    private $proccessInfo;

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

        if ($result->status) {
            $this->info("| {$result->message}");
        } else {
            $this->error("| {$result->message}");
            return;
        }

        $this->Posinstall();

        $this->alert("Finish service");
    }

    protected function Posinstall()
    {
        $composer = env('COMPOSER_ALIASE', 'composer');
        Artisan::call('migrate');
        Artisan::call('optimize:clear');
        $this->warn("| Update composer");
        $update = $this->ExecuteCommand("{$composer} update -W");
        if ($update->status) {
            $install = $this->ExecuteCommand("{$composer} install -W");
            if ($install->status) {
                $this->info($install->buffer);
                $this->info($install->message);
            } else {
                $this->info($install->buffer);
                $this->error($install->message);
            }
        } else {
            $this->info($update->buffer);
            $this->error($update->message);
        }
    }

    private function ExecuteCommand(string $command)
    {
        $this->info('| ℹ️  Executando comando: ' . $command);
        $process = Process::fromShellCommandline($command);
        $process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->proccessInfo = (object)[
                    'status' => false,
                    'message' => "| ❌ ERROR: um erro aconteceu ao executar o processo",
                    'buffer' => $buffer
                ];
            } else {
                $this->proccessInfo = (object) [
                    'status' => true,
                    'message' => "| ✅ Comando executado com sucesso!",
                    'buffer'  => $buffer
                ];
            }
        });

        // Aguarda até que o processo seja iniciado
        while ($process->isRunning()) {
            sleep(1);
        }

        if (isset($this->proccessInfo)) {
            return $this->proccessInfo;
        }

        return (object) [
            'status' => false,
            'message' => "| Processo retornou null ou vazio",
            "buffer" => "No buffer"
        ];
    }
}
