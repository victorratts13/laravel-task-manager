<?php

namespace App\Http\Controllers;

use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Store\StringStore;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackEndController extends Controller
{

    public static string $utils = UtilsController::class;
    
    public function Supervisor()
    {
        try {
            $key = env('APP_KEY');
            Artisan::call('app:supervisor', ['--id' => $key, '--restart' => true]);
            
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
        // $repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();

        // Usa o StringStore para criar um armazenamento a partir da string
        $store = new StringStore($envString);

        // Analisa o armazenamento
        $parser = new Parser();
        $entries = $parser->parse($store->read());
        foreach ($entries as $entry) {
            $envKey = $entry->getName();
            $envItem = (string)$entry->getValue()->get()->getChars();
            $_ENV[$envKey] = $envItem;
            putenv("{$envKey}={$envItem}");
        }
        return (object)$_ENV;
    }

    public static function GetLastVersion(string $repository)
    {
        try {
            $client = new Client();
            $content = collect(json_decode($client->get("https://api.github.com/repos/{$repository}/releases")->getBody()->getContents()));
            return $content->first();
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

    // removes files and non-empty directories
    private static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") static::rrmdir("$dir/$file");
            rmdir($dir);
        } else if (file_exists($dir)) unlink($dir);
    }

    // copies files and non-empty directories
    private static function rcopy($src, $dst)
    {
        if (file_exists($dst)) static::rrmdir($dst);
        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..") static::rcopy("$src/$file", "$dst/$file");
        } else if (file_exists($src)) copy($src, $dst);
    }

    private static function moveFiles($origem, $destino)
    {
        // Verifica se o diretório de origem existe
        if (!File::exists($origem)) {
            throw new Exception("O diretório de origem não existe: {$origem}");
        }

        // Verifica se o diretório de destino existe, caso contrário, cria-o
        if (!File::exists($destino)) {
            File::makeDirectory($destino, 0755, true);
        }

        $temp = $origem . ".zip";

        $zipFile = UtilsController::MakeZipFile($temp);
        static::extractZip($temp, $destino);
        File::delete($temp);
        dump($zipFile);

    }

    protected static function command(string $command)
    {
        try {
            exec($command);
            return (object)[
                'status' => true,
                'message' => "Exec with success"
            ];
        } catch (\Throwable $th) {
            return (object)[
                'status' => false,
                'message' => $th->getMessage()
            ];
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
            
            unlink($tempPath);

            return (object)[
                'status' => true,
                'message' => "Install with success"
            ];
        } catch (\Throwable $th) {
            // dd($th);
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
