<?php

namespace App\Http\Controllers;

use App\Models\ServiceProccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Madnest\Madzipper\Madzipper;
use Symfony\Component\Process\Process;

class UtilsController extends Controller
{

    public object $proccessInfo;

    public static function MakeZipFile($zipFilename)
    {

        // Caminho para o arquivo zip

        if (!strpos("/", $zipFilename)) {
            $zipFilePath = base_path($zipFilename);
        } else {
            $zipFilePath = $zipFilename;
        }

        // Iniciar a criação do arquivo zip
        $zipper = new Madzipper();

        // File::put('filemap.json', json_encode($files));

        $zipper->make($zipFilePath);

        foreach (static::getFilesToZip() as $file) {
            $zipper->add($file['absolutePath'], $file['relativePath']);
        }

        $zipper->close();

        return $zipFilePath;
    }

    public static function addresingFilestoZip(): array
    {
        $onlyFiles = ['app/', 'bootstrap/', 'config/', 'database/', 'public/', 'resources/', 'routes/', '.env.example', '.htaccess', 'artisan', 'composer.json', 'index.php', 'package.json', 'phpunit.xml', 'vite.config.js'];
        $addresingFiles = [];
        foreach ($onlyFiles as $value) {
            $addresingFiles[$value] = __DIR__ . '/../../../' . $value;
        }

        return $addresingFiles;
    }

    public static function getFilesToZip(): array
    {
        $filesToZip = [];

        foreach (static::addresingFilestoZip() as $name => $path) {

            if (File::isDirectory(base_path($name))) {
                $allFiles = File::allFiles(base_path($name));

                foreach ($allFiles as $file) {
                    $relativePath = $file->getRelativePathname();
                    if (!str_starts_with($relativePath, '.vscode') && $file->getFilename() !== '.env') {
                        // $filesToZip[] = $file->getPathname();
                        $filesToZip[] = [
                            'absolutePath' => $file->getRealPath(),
                            'relativePath' => $name . $relativePath
                        ];
                    }
                }
            } else {
                // $filesToZip[] = $path;
                $filesToZip[] = [
                    'absolutePath' => $path,
                    'relativePath' => $name
                ];
            }
        }

        return $filesToZip;
    }

    public static function nameDir($path)
    {
        return array_reverse(explode("/", $path))[0];
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

    public function monitor()
    {

        $processes = array();
        $output = Cache::get('task-manager-provider');

        if (isset($output)) {
            foreach ($output as $key => $line) {
                $columns = preg_split('/\s+/', $line);
                if (count($columns) >= 11) {
                    $processes[] = (object)[
                        'id' => $key,
                        'user' => $columns[0],
                        'pid' => intval($columns[1]),
                        'cpu' => ($columns[2]),
                        'mem' => ($columns[3]),
                        'vsz' => $columns[4],
                        'rss' => $columns[5],
                        'tt' => $columns[6],
                        'stat' => $columns[7],
                        'started' => $columns[8],
                        'software' => $columns[10],
                        'command' => implode(' ', array_slice($columns, 10)),
                        'time' => $this->convertTimeToSeconds($columns[9]),
                    ];
                }
            }

            unset($processes[0]);

            return collect($processes);
        } else {
            return collect([]);
        }
    }



    public function CheckComandStatus(string $comand)
    {
        return $this->monitor()->filter(function ($mp) use ($comand) {
            if (strpos($mp->command, $comand)) {
                return true;
            } else if (strpos($comand, $mp->command)) {
                return true;
            } else {
                return false;
            }
        })->first() ?? false;
    }

    public function ExecuteCommand(string $command)
    {
        $process = Process::fromShellCommandline($command);
        $process->start(function ($type, $buffer) use ($process){
            if (Process::ERR === $type) {
                $this->proccessInfo = (object)[
                    'status' => false,
                    'message' => "| ❌ ERROR: Fail to execute the process, please, update process",
                    'buffer' => $buffer,
                    'pid' => 0
                ];
            } else {
                $this->proccessInfo = (object) [
                    'status' => true,
                    'message' => "| ✅ Command executed withsuccess!",
                    'buffer'  => $buffer,
                    'pid' => 0
                ];
            }
        });

        $pid = $process->getPid();

        // Aguarda até que o processo seja iniciado
        while ($process->isRunning()) {
            sleep(1);
        }

        if (isset($this->proccessInfo)) {
            $this->proccessInfo->pid = $pid;
            return $this->proccessInfo;
        }

        return (object) [
            'status' => false,
            'pid' => 0,
            'message' => "| Processo retornou null ou vazio",
            'buffer' => "****** Warning ****** \n| No buffer logs is generated for this process."
        ];
    }
}
