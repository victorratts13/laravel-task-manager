<?php

namespace App\Http\Controllers;

use App\Models\ServiceProccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

class TaskManagerController extends Controller
{

    public string $command;
    public Collection $index;
    public UtilsController $utils;

    public function __construct(string $command)
    {
        $this->command = $command;
        $this->utils = new UtilsController();
    }

    public function psaux()
    {
        $this->index = $this->utils->monitor();
        return $this->index;
    }

    public function status()
    {
        $command = $this->command;
        $status = $this->utils->CheckComandStatus($command);
        Log::info(["command" => $command, "status" => $status]);
        return $status;
        // dd($status);
        // return $this
        //     ->psaux()
        //     ->filter(function ($mp) use ($command) {
        //         return $;
        //     })
        //     ->first() ?? false;
    }

    public function kill() {
        return $this->utils->ExecuteCommand("pkill -f '{$this->command}'");
    }
}
