<?php

namespace App\Support;

use App\Models\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Repositories
{
    /** @var string */
    protected const CACHE_KEY = 'docs.repos';

    public function clear(): self
    {
        Cache::forget(static::CACHE_KEY);

        return $this;
    }

    public function all(): Collection
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            static function () {
                $repos = json_decode(file_get_contents(resource_path('json/repositories.json')), true);

                return collect($repos)
                    ->map(fn (array $repo) => new Repository($repo));
            }
        );
    }

    public function find(string $name): ?Repository
    {
        return $this
            ->all()
            ->where('name', $name)
            ->first();
    }

    public function byCategory(): Collection
    {
        return $this->all()->groupBy('category');
    }
}
