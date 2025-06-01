<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Services\ClientService;
use App\Application\Services\PlanService;
use App\Application\Services\TenantService;
use App\Application\Interfaces\ClientServiceInterface;
use App\Application\Interfaces\PlanServiceInterface;
use App\Application\Interfaces\TenantServiceInterface;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind application services to their implementations
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(PlanServiceInterface::class, PlanService::class);
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
