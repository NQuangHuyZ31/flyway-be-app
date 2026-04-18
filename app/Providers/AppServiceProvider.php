<?php

namespace App\Providers;

use App\Services\StorageImage\S3StorageImageService;
use App\Services\StorageImage\StorageImageServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(StorageImageServiceInterface::class, function ($app) {
            return match (config('app.default_storage')) {
                's3' => $app->make(S3StorageImageService::class),
                 default => $app->make(S3StorageImageService::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
