<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use TitasGailius\Terminal\Terminal;

class ExportDocs extends Command
{
    protected $signature = 'docs:export
                            {repository : The repository to export the docs for}
                            {version : The version of docs to export}
                            {--target= : The target directory to copy the docs to}
    ';

    protected $description = 'Export docs created here to back to .md format to put in the package repository.';

    public function handle(): void
    {
        $repository = Repositories::find($this->argument('repository'));

        if (! $repository) {
            throw new InvalidArgumentException("Repository '{$this->argument('repository')} not found.");
        }

        if (! $repository->hasVersion($this->argument('version'))) {
            throw new InvalidArgumentException("Version '{$this->argument('version')} does not exist on this repository.");
        }

        $this->info("Exporting version {$this->argument('version')} docs to .md format for {$repository->name}");

        if (! File::isDirectory(dirname($this->exportDir()))) {
            File::makeDirectory(dirname($this->exportDir()), 0755, true);
        }

        // We want a fresh copy directory
        if (File::isDirectory($this->exportDir())) {
            File::deleteDirectory($this->exportDir());
        }

        $resourcePath = resource_path("views/docs/{$repository->name}/{$this->argument('version')}");

        Terminal::run(
            "cp -r {$resourcePath} {$this->exportDir()}"
        );

        $this->convert();

        if ($this->option('target')) {
            $target = config('site.packages_directory') . $this->option('target') . '/' . config('site.default_package_docs_directory');

            $this->warn("Copying converted .md files to: {$target}");

            Terminal::run(
                "cp -r {$this->exportDir()} {$target}"
            );
        }

        if ($this->option('target') && $this->ask('Delete files in temporary folder? [y/n]', 'y') === 'y') {
            File::deleteDirectory(dirname($this->exportDir()));
        }

        $this->info('Export finished.');
    }

    protected function exportDir(): string
    {
        return __DIR__ . '/export/' . $this->argument('repository') . '/' . $this->argument('version');
    }

    protected function convert(): void
    {
        $files = Finder::create()
            ->in($this->exportDir());

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir() || Str::startsWith($file->getFilename(), '_index')) {
                continue;
            }

            $fileName = str_replace('.blade', '', $file->getFilenameWithoutExtension());
            $path = $file->getPath();
            $newPath = "{$path}/{$fileName}.md";

            File::move(
                $file->getRealPath(),
                $newPath
            );
        }
    }
}
