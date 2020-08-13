<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Support\Repositories
 *
 * @method static self clear()
 * @method static \Illuminate\Support\Collection all()
 * @method static \App\Models\Repository|null find(string $name)
 * @method static \Illuminate\Support\Collection byCategory()
 */
class Repositories extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\Repositories::class;
    }
}
