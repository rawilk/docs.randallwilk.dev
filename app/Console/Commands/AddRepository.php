<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use App\Models\Repository;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AddRepository extends Command
{
    protected $signature = 'docs:add-repo
                            {--repository= : Optionally specify the repository name}
                            {--repo-version= : Optionally specify the repository version}
    ';

    protected $description = 'Add a repository to repositories.json file.';

    protected ?Repository $repository;

    public function handle(): void
    {
        $name = $this->getRepositoryName();

        $this->setRepositoryModel($name);

        $remoteRepository = $this->getRepository();
        $version = $this->getVersion();
        $category = $this->getCategory();
        $description = $this->getRepoDescription();

        if (! $this->shouldCreate($version)) {
            return;
        }

        $allRepositories = Repositories::all();

        if ($this->repository) {
            $allRepositories = $allRepositories->filter(fn (Repository $r) => $r->name !== $name)->values();
        }

        $allRepositories = $allRepositories->toArray();

        $branches = $this->repository
            ? $this->repository->branches
            : [];

        if (array_key_exists('master', $branches)) {
            $branches[$version] = $version;
        } else {
            $branches['master'] = $version;
        }

        $allRepositories[] = [
            'name' => $name,
            'description' => $description,
            'repository' => $remoteRepository,
            'branches' => $branches,
            'category' => $category,
        ];

        file_put_contents(
            resource_path('json/repositories.json'),
            str_replace("\/", '/', json_encode($allRepositories, JSON_PRETTY_PRINT)) . PHP_EOL
        );

        Repositories::clear();

        $this->info("Repository '{$name}' version '{$version}' was added.");
    }

    protected function shouldCreate(string $version): bool
    {
        if (! $this->repository) {
            return true;
        }

        if ($this->repository->hasVersion($version)) {
            $this->error("Version '{$version}' is already defined for '{$this->repository->name}'.");

            return false;
        }

        return true;
    }

    protected function getRepositoryName(): string
    {
        if (! $name = $this->option('repository')) {
            $name = $this->ask('Name');
        }

        $name = Str::slug($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Repository name is required.');
        }

        return $name;
    }

    protected function setRepositoryModel(string $name): void
    {
        $this->repository = Repositories::find($name);
    }

    protected function getRepository(): string
    {
        if ($this->repository) {
            return $this->repository->repository;
        }

        $repository = trim($this->ask('Repository [username/repository-name]'));

        if (empty($repository)) {
            throw new InvalidArgumentException('Repository is required.');
        }

        return $repository;
    }

    protected function getVersion(): string
    {
        if (! $version = $this->option('repo-version')) {
            $version = $this->ask('Version');
        }

        $version = strtolower(trim($version));

        if (! Str::startsWith($version, 'v')) {
            throw new InvalidArgumentException('Version must start with "v".');
        }

        return $version;
    }

    protected function getRepoDescription(): string
    {
        if ($this->repository) {
            return $this->repository->description;
        }

        $description = $this->ask('Description (optional)');

        return $description ?? '';
    }

    protected function getCategory(): string
    {
        if ($this->repository) {
            return $this->repository->category;
        }

        $category = trim($this->ask('Category', 'Laravel'));

        if (empty($category)) {
            throw new InvalidArgumentException('Category is required');
        }

        return $category;
    }
}
