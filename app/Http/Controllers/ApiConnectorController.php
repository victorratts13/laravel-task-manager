<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ApiConnectorController extends Controller
{
    public Client $client;
    private string $apikey;
    public function __construct()
    {
        $this->apikey = (new TaskManagerController)->apikey;
        $this->client = new Client([
            'base_uri' => env("APP_URL")
            /**
             * Insert any Network Config Here
             */
        ]);
    }

    /**
     * Exec command and get 
     * PID from process
     */
    public function RunCommandAndGetPid(string $command, bool $isArray = false) {
        $response = $this->client->post("/api/v1/pid", [
            'json' => [
                'command' => $command
            ],
            'headers' => [
                'api-key' => $this->apikey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), $isArray);
    }
}
