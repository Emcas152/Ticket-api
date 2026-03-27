<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', fn () => new Filesystem());
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
