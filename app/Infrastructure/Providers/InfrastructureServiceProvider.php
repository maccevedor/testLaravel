<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Http\Resources\ClientResource;
use App\Infrastructure\Http\Resources\PlanResource;
use App\Infrastructure\Http\Resources\TenantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register infrastructure-specific bindings
        $this->app->bind('client.resource', ClientResource::class);
        $this->app->bind('plan.resource', PlanResource::class);
        $this->app->bind('tenant.resource', TenantResource::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure JSON resource serialization
        JsonResource::withoutWrapping();
    }
}
