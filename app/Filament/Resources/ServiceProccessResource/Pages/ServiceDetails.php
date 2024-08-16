<?php

namespace App\Filament\Resources\ServiceProccessResource\Pages;

use App\Filament\Resources\ServiceProccessResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;

class ServiceDetails extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ServiceProccessResource::class;

    protected static string $view = 'filament.resources.service-proccess-resource.pages.service-details';

    public object $commandSource;

    public array $commandSOurceList = [
        'bash' => [
            'name' => "Bash",
            'fullname' => "Bourne-Again SHell",
            'image' => "/icons/bash.svg",
            'isDefault' => true,
            'commands' => null
        ],
        'php' => [
            'name' => "PHP",
            'fullname' => "PHP: Hypertext Preprocessor",
            'image' => "/icons/php.svg",
            'isDefault' => false,
            'commands' => ['php', 'php-fpm']
        ],
        'python' => [
            'name' => "Python",
            'fullname' => "Python",
            'image' => "/icons/python.svg",
            'isDefault' => false,
            'commands' => ['python', 'python2', 'python3', 'pip', 'py']

        ],
        'laravel' => [
            'name' => "Laravel",
            'fullname' => "Laravel",
            'image' => "/icons/laravel.svg",
            'isDefault' => false,
            'commands' => ['artisan']

        ],
        'nodejs' => [
            'name' => "NodeJs",
            'fullname' => "NodeJs",
            'image' => "/icons/node-js.svg",
            'isDefault' => false,
            'commands' => ['node', 'npm', 'npx']
        ],
        'java' => [
            'name' => "Java",
            'fullname' => "Java enviroment",
            'image' => "/icons/java-original.svg",
            'isDefault' => false,
            'commands' => ['java', 'javac']
        ],
        'git' => [
            'name' => "Git",
            'fullname' => "Git",
            'image' => "/icons/git.svg",
            'isDefault' => false,
            'commands' => ['git']
        ],
        'golang' => [
            'name' => "Go",
            'fullname' => "Go Lang",
            'image' => "/icons/golang.svg",
            'isDefault' => false,
            'commands' => ['go']
        ],
        'rust' => [
            'name' => "Rust",
            'fullname' => "Rust",
            'image' => "/icons/rust.svg",
            'isDefault' => false,
            'commands' => ['cargo', 'rustc']
        ],
        'docker' => [
            'name' => "Docker",
            'fullname' => "Docker",
            'image' => "/icons/docker.svg",
            'isDefault' => false,
            'commands' => ['docker']
        ],
        'curl' => [
            'name' => "curl",
            'fullname' => "cURL",
            'image' => "/icons/curl.svg",
            'isDefault' => false,
            'commands' => ['curl']
        ],
    ];

    private static function determineSoftware($command) {
        // Define a list of patterns and corresponding software
        $patterns = [
            'bash' => '/^(ls|cd|mv|cp|rm|echo|cat|grep|find|chmod|chown|mkdir|rmdir|touch|sudo|apt|yum|\.\/)/',
            'laravel' => '/^php artisan/',
            'php' => '/^php( |$)/',
            'python' => '/^python[0-9]* /',
            'nodejs' => '/^(node|npm|npx) /',
            'ruby' => '/^(ruby|rails|rake|gem) /',
            'java' => '/^(java|javac) /',
            'golang' => '/^(go) /',
            'rust' => '/^(cargo|rustc) /',
            'docker' => '/^(docker|docker-compose) /',
            'git' => '/^git /',
            'composer' => '/^composer /',
            'make' => '/^make /',
            'perl' => '/^perl /',
            'curl' => '/^curl /',
            'wget' => '/^wget /',
            'powershell' => '/^(powershell|pwsh) /',
            'ansible' => '/^ansible /',
            'terraform' => '/^terraform /',
        ];
    
        // Iterate over the patterns and return the corresponding software if a match is found
        foreach ($patterns as $software => $pattern) {
            if (preg_match($pattern, $command)) {
                return $software;
            }
        }
    
        // Return 'unknown' if no pattern matches
        return 'bash';
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->dispatch('page-process-details', record: $this->record);
    }

    #[On('determine-command-type')]
    public function DetermineCommandType(string $command)
    {   
        // dd($command);
        $type = static::determineSoftware($command);
        // dd($type);
        $this->commandSource = (object)$this->commandSOurceList[$type];
    }
}
