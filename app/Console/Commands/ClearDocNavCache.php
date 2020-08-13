<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use App\Models\Repository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class ClearDocNavCache extends Command
{
    protected $signature = 'docs:clear-doc-nav-cache
                            {--repository= : Only clear the navigation cache for a single repository}
                            {--branch= : Only clear the navigation cache for a specific branch}
    ';

    protected $description = 'Clear the navigation cache for a repository.';

    public function handle(): void
    {
        Cache::forget('repository_nav.*');

        foreach ($this->repositories() as $repository) {
            $this->clearRepositoryCache($repository);
        }

        $this->info('Document navigation cache was cleared.');
    }

    protected function clearRepositoryCache(Repository $repository): void
    {
        $this->info("Clearing navigation cache for '{$repository->name}'...");

        if ($this->option('branch')) {
            $this->clearBranchCache($repository, $this->option('branch'));

            return;
        }

        foreach ($repository->branches as $branch => $alias) {
            $this->clearBranchCache($repository, $alias);
        }
    }

    protected function clearBranchCache(Repository $repository, string $alias): void
    {
        if (! $repository->hasVersion($alias)) {
            return;
        }

        Cache::forget("repository_nav.{$repository->name}.{$alias}");
    }

    protected function repositories(): Collection
    {
        if ($this->option('repository')) {
            $repository = Repositories::find($this->option('repository'));

            if (! $repository) {
                throw new InvalidArgumentException("Repository `{$this->option('repository')}` is not defined in repositories.json file.");
            }

            return collect([$repository]);
        }

        return Repositories::all();
    }
}
