<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TaskManagerController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiFirewall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $localKey = (new TaskManagerController())->apikey;
        $remoteKey = $request->header('api-key');

        /**
         * Check if RemoteKey is 
         * inserted on Header request
         */
        if(!isset($remoteKey)){
            return response()->json([
                'status' => false,
                'message' => "API key is required!"
            ], 403);
        }

        /**
         * Check if RemoteKey is a 
         * Valid API key Header Request
         */

         if($remoteKey !== $localKey){
            return response()->json([
                'status' => false,
                'message' => "API key is invalid!"
            ], 403);
         }

        return $next($request);
    }
}
