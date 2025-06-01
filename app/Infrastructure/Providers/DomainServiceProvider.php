<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ClientRepository;
use App\Infrastructure\Persistence\Eloquent\PlanRepository;
use App\Infrastructure\Persistence\Eloquent\TenantRepository;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind domain repositories to their implementations
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
