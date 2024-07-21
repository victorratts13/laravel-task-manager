<?php

namespace App\Jobs;

use App\Http\Controllers\BackEndController;
use App\Models\Enviromet;
use App\Models\ServiceLogs;
use App\Models\ServiceProccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ComandExecJob implements ShouldQueue
{
    use Queueable;
    public $proccess;
    private object $proccessInfo;

    /**
     * Create a new job instance.
     */
    public function __construct($proccess)
    {
        // $this->onQueue('task-manager');
        $this->proccess = $proccess;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $backend = new BackEndController();
        $enviroment = $this->proccess->enviroment()->first();
        
        if(isset($enviroment->variables)){
            $backend->loadEnvironmentFromString($enviroment->variables);
        }

        if(!$this->CheckComandStatus($this->proccess->command)){
            $prepare = "cd {$this->proccess->enviroment()->first()->path} && {$this->proccess->command}";
            $this->ExecuteCommand($prepare);
            // exec($prepare);
            $proccessInfo = $this->getProcessInfo($this->proccess->command)->first();
            if(isset($proccessInfo)){
                ServiceProccess::where('id', $this->proccess->id)->first()->update(['pid' => $proccessInfo->pid]);
            }
            
            if($this->proccess->loggable){
                ServiceLogs::create([
                    'service' => $this->proccess->id,
                    'command' => $this->proccess->command,
                    'output' => $this->proccessInfo->buffer
                ]);
            }
        } else {
            $proccessInfo = $this->getProcessInfo($this->proccess->command)->first();
            if(isset($proccessInfo)){
                ServiceProccess::where('id', $this->proccess->id)->first()->update(['pid' => $proccessInfo->pid]);
            }
        }
    }

    private function ExecuteCommand(string $command)
    {
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
        return strpos($process->buffer, $comand);
    }

    private function GetQueueNames() {
        $queues = DB::table('jobs')->select('queue')->distinct()->pluck('queue');
        return $queues;
    }

    public function getProcessInfo($commandName)
    {
        // Executa o comando ps com os parâmetros para obter as informações desejadas
        $output = [];
        $command = "ps aux | grep '{$commandName}' | grep -v grep";
        exec($command, $output);

        // Processa a saída do comando para extrair as informações necessárias
        $processes = [];
        foreach ($output as $line) {
            $columns = preg_split('/\s+/', $line);

            if (count($columns) >= 11) {
                $processes[] = (object)[
                    'pid' => $columns[1],
                    'user' => $columns[0],
                    'cpu' => $columns[2],
                    'mem' => $columns[3],
                    'time' => $this->convertTimeToSeconds($columns[9]),
                ];
            }
        }

        return collect($processes);
    }

    private function convertTimeToSeconds($time)
    {
        $parts = explode(':', $time);
        $seconds = 0;
        if (count($parts) == 3) {
            $seconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        } elseif (count($parts) == 2) {
            $seconds = ($parts[0] * 60) + $parts[1];
        }
        return $seconds;
    }
}
