<?php

namespace App\Http\Controllers;

use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Store\StringStore;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackEndController extends Controller
{
    public function Supervisor()
    {
        try {
            $key = env('APP_KEY');
            Artisan::call('app:supervisor', ['--id' => $key, '--restart' => true]);
            // Process::fromShellCommandline("php artisan app:supervisor --restart --id={$key} &");
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

    function loadEnvironmentFromString($envString)
    {
        // Cria um repositório para armazenar as variáveis de ambiente
        $repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();

        // Usa o StringStore para criar um armazenamento a partir da string
        $store = new StringStore($envString);

        // Analisa o armazenamento
        $parser = new Parser();
        $entries = $parser->parse($store->read());
        foreach ($entries as $entry) {
            $_ENV[$entry->getName()] = (string)$entry->getValue()->get()->getChars();
        }
        return (object)$_ENV;
    }

    public static function GetLastVersion(string $repository)
    {
        try {
            $client = new Client();
            $content = collect(json_decode($client->get("https://api.github.com/repos/{$repository}/releases")->getBody()->getContents()));
            return $content->reverse()->first();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function GetVersion(string $repository, string | int $version)
    {
        try {
            $client = new Client();
            $content = collect(json_decode($client->get("https://api.github.com/repos/{$repository}/releases")->getBody()->getContents()));

            return $content->filter(function ($mp) use ($version) {
                if ($version == $mp->name || $version == $mp->tag_name) {
                    return true;
                }

                return false;
            })->first();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function downloadAndUpdate(string $url)
    {
        try {
            $client = new Client();
            // Nome do arquivo zip temporário
            $zipFileName = 'update.zip';
            $tempPath = storage_path('app/' . $zipFileName);

            // Baixa o arquivo zip
            $client->get($url, ['sink' => $tempPath]);

            // Extrai o zip
            static::extractZip($tempPath, base_path());

            // Remove o arquivo zip temporário
            unlink($tempPath);

            return (object)[
                'status' => true,
                'message' => "Install with success"
            ];
        } catch (\Throwable $th) {
            return (object)[
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    protected static function extractZip(string $zipFile, string $extractTo)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
        } else {
            throw new \Exception('Failed to extract the zip file');
        }
    }
}
