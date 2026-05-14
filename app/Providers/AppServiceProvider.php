<?php

namespace App\Providers;

use App\Repositories\ServerCopyRepository;
use App\Services\NidApiService;
use App\Services\PdfGeneratorService;
use App\Services\ServerCopyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind services as singletons (one instance per request)
        $this->app->singleton(NidApiService::class);
        $this->app->singleton(PdfGeneratorService::class);
        $this->app->singleton(ServerCopyRepository::class);

        $this->app->singleton(ServerCopyService::class, function ($app) {
            return new ServerCopyService(
                $app->make(NidApiService::class),
                $app->make(ServerCopyRepository::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
