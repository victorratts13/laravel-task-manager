<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InternalApiV1Controller extends Controller
{
    public function pid(Request $reques) {
        $pid = (new TaskManagerController())->ExecPid($reques->command);
        return (object)[
            'status' => true,
            'pid' => $pid->pid,
            'buffer' => $pid->buffer
        ];
    }

    public function monitor() {
        $psaux = (new TaskManagerController())->psaux();
        return (object)[
            'status' => true,
            'process' => $psaux->values()
        ];
    }
}
