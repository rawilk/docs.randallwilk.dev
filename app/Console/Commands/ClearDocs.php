<?php

namespace App\Console\Commands;

use App\Facades\Repositories;
use Illuminate\Console\Command;

class ClearDocs extends Command
{
    protected $signature = 'docs:clear';

    protected $description = 'Clear the doc repository cache.';

    public function handle(): void
    {
        Repositories::clear();

        $this->info('Document repository cache cleared!');
    }
}
