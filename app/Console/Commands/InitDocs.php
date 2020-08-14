<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use App\Models\Repository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InitDocs extends Command
{
    protected $signature = 'docs:init
                            {repository : The repository to create a new doc version for}
                            {version : The version you are creating}
    ';

    protected $description = 'Create a starting point for a new document version.';

    public function handle(): void
    {
        $version = strtolower($this->argument('version'));

        if (! Str::startsWith($version, 'v')) {
            throw new InvalidArgumentException('Version must start with "v"');
        }

        $repository = $this->repository();

        if ($this->hasDocs($repository, $version)) {
            throw new InvalidArgumentException("{$repository->name} already has version '{$version}' docs created for it.");
        }

        File::makeDirectory(resource_path("views/docs/{$repository->name}/{$version}"), 0755, true);

        $stubs = [
            'introduction' => [],
            'installation' => [
                'NAME' => $repository->name,
                'REPOSITORY' => $repository->repository,
            ],
            'questions-and-issues' => [
                'REPOSITORY' => $repository->repository,
            ],
            'requirements' => [],
            'changelog' => [
                'NAME' => $repository->name,
                'REPOSITORY' => $repository->repository,
            ],
        ];

        foreach ($stubs as $filename => $replacements) {
            $content = File::get(__DIR__ . "/stubs/{$filename}.stub");
            $replacementKeys = array_map(fn (string $key) => '{{ ' . $key . ' }}', array_keys($replacements));
            $content = str_replace($replacementKeys, array_values($replacements), $content);

            $newFilename = resource_path("views/docs/{$repository->name}/{$version}/$filename.blade.php");

            File::put($newFilename, $content);
        }

        $this->info("Documents created for {$repository->name} {$version}.");
    }

    protected function repository(): Repository
    {
        $repository = Repositories::find($this->argument('repository'));

        if (! $repository) {
            if ($this->ask("Repository '{$this->argument('repository')}' does not exist. Add it now? [y/n]", 'y') === 'y') {
                return $this->addRepository();
            }

            throw new InvalidArgumentException("Repository '{$this->argument('repository')}' not found in repositories.json file.");
        }

        return $repository;
    }

    protected function addRepository(): Repository
    {
        $this->call('docs:add-repo', [
            '--repository' => $this->argument('repository'),
            '--repo-version' => $this->argument('version'),
        ]);

        return Repositories::find($this->argument('repository'));
    }

    protected function hasDocs(Repository $repository, string $version): bool
    {
        $path = resource_path("views/docs/{$repository->name}/{$version}");

        return file_exists($path);
    }
}
