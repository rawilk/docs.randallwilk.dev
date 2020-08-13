<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class UpdateDocs extends Command
{
    protected $signature = 'docs:update
                            {--repository= : Only update docs for a specific repository}
    ';

    protected $description = 'Update the docs from all configured packages.';

    public function handle(): void
    {
        $this->call(ClearDocs::class);

        $this->info('Fetching repository docs...');
        $this->info('---------------------------');

        /** @var \App\Models\Repository $repository */
        foreach ($this->repositories() as $repository) {
            $this->info('Fetching docs for: ' . $repository->name . '...');

            $repository->updateDocs();

            $this->warn('Docs were fetched for: ' . $repository->name);
        }

        $this->info('Done fetching docs');
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
