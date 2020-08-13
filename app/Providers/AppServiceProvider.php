<?php

namespace App\Providers;

use App\Support\Repositories;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repositories::class, fn () => new Repositories);
    }

    public function boot(): void
    {
    }
}
