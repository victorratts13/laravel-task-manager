<?php

namespace App\Http\Controllers;

use App\Models\ServiceProccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Component\Process\Process;

class TaskManagerController extends Controller
{

    public string $command;
    public mixed $pid;
    public string $apikey;
    public string $uffer;
    public Collection $index;
    public UtilsController $utils;

    public function __construct(string $command = "")
    {
        $this->command = $command;
        $this->apikey = hash('sha256', env('APP_KEY'));
        $this->utils = new UtilsController();
    }

    public function psaux()
    {
        $this->index = $this->utils->monitor();
        return $this->index;
    }

    public function ExecPid($command)
    {
        // $command = $command . " > /dev/null 2>&1 & echo $!";
        $command = $command . " &";
        $pid = $this->utils->ExecuteCommand($command);
        $this->pid = $pid->pid;
        return $pid;
    }

    public function status()
    {
        $command = $this->command;
        $status = $this->utils->CheckComandStatus($command);
        return $status;
    }

    public function kill(int $pid = 0)
    {
        $kill = false;
        if($pid > 0){
            $kill = $this->utils->ExecuteCommand("kill {$pid}");
            if(posix_kill($pid, 0)){
                return $kill;
            }
        } else if(!empty($this->command)){
            $kill = $this->utils->ExecuteCommand("pkill -f '{$this->command}'");
            if(posix_kill($pid, 0)){
                return $kill;
            }
        } else if(!empty($this->pid)){
            $kill = $this->utils->ExecuteCommand("kill -f {$this->pid}");
            if(posix_kill($pid, 0)){
                return $kill;
            }
        }

        return $kill;
    }

    public function execute(string $command)
    {
        $connector = (new ApiConnectorController())->RunCommandAndGetPid($command);
        return $connector;
    }
}
