<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Madnest\Madzipper\Madzipper;

class UtilsController extends Controller
{

    public static function MakeZipFile($zipFilename)
    {

        // Caminho para o arquivo zip
        $zipFilePath = base_path($zipFilename);

        // Iniciar a criação do arquivo zip
        $zipper = new Madzipper();

        // File::put('filemap.json', json_encode($files));

        $zipper->make($zipFilePath);

        foreach (static::getFilesToZip() as $file) {
            $zipper->add($file['absolutePath'], $file['relativePath']);
        }

        $zipper->close();

        return base_path("{$zipFilename}");
    }

    public static function addresingFilestoZip(): array
    {
        $onlyFiles = ['app/', 'bootstrap/', 'config/', 'database/', 'public/', 'resources/', 'routes/', 'storage/', 'tests/', '.env.example', '.htaccess', 'artisan', 'composer.json', 'index.php', 'package.json', 'phpunit.xml', 'vite.config.js'];
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

    public static function nameDir($path) {
        return array_reverse(explode("/", $path))[0];
    }
}
