<?php

namespace App\Console\Commands;

use App\Http\Controllers\ArbitrageController;
use App\Models\Queues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Supervisor extends Command
{
    private object $proccessInfo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:supervisor {--restart} {--stop} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supervisor do sistema que mantem os processos em execução';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $restart = $this->option('restart');
        $stop = $this->option('stop');
        $id = $this->option('id');

        $this->alert("Supervisor 1.0.6");

        $checkComandProccess = [
            "app:task-manager --id={$id}" => "app:task-manager --id={$id} &",
        ];

        if(Queues::count() > 0){
            foreach(Queues::all() as $queue){
                if($queue->status){
                    $name = Str::slug($queue->name);
                    $checkComandProccess["queue:listen --queue={$name} --memory={$queue->memory}"] = "php artisan queue:listen --queue={$name} --memory={$queue->memory} &";
                }
            }
        }

        foreach ($checkComandProccess as $check => $comand) {
            $checker = $this->CheckComandStatus($check);
            if (!$checker) {
                // O comando não está em execução, então inicie-o
                $command = $this->ExecuteCommand($comand);
                $checker = $this->CheckComandStatus($check);
                if ($checker) {
                    $this->info($command->message);
                } else {
                    $this->error($command->message);
                }
            } else {
                $this->info("| ⚠️  Comando ja está em execução");
                if($restart){
                    $this->info("| 📒  Reiniciando processo...");
                    $this->ExecuteCommand("pkill -f '{$check}'");
                    $command = $this->ExecuteCommand($comand);
                    $this->info($command->message);
                } else if($stop) {
                    $this->info("| 🔴  Parando processo...");
                    $this->ExecuteCommand("pkill -f '{$check}'");
                }
            }
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
            'message' => "| Processo retornou null ou vazio"
        ];
    }

    private function CheckComandStatus(string $comand)
    {
        $process = $this->ExecuteCommand('ps aux');
        // dd($process);
        // dd(strpos($process->buffer, $comand));
        return strpos($process->buffer, $comand);
    }

    private function GetQueueNames() {
        $queues = DB::table('jobs')->select('queue')->distinct()->pluck('queue');
        return $queues;
    }
}
