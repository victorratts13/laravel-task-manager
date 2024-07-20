<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackEndController extends Controller
{
    public function Supervisor() {
        try {
            $key = env('APP_KEY');
            Artisan::call('app:supervisor', ['--id' => $key]);

            return response()->json([
                'status' => true,
                'message' => "Supervisor is running!"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
