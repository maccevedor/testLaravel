<?php

namespace App\Providers;

use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\ClientRepository;
use App\Infrastructure\Persistence\Repositories\PlanRepository;
use App\Infrastructure\Persistence\Repositories\TenantRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
    }
}
