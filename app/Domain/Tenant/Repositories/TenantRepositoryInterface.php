<?php

namespace App\Domain\Tenant\Repositories;

use App\Domain\Tenant\Entities\Tenant;

interface TenantRepositoryInterface
{
    public function findById(int $id): ?Tenant;
    public function findAll(): array;
    public function save(Tenant $tenant): void;
    public function update(Tenant $tenant): void;
    public function delete(int $id): void;
}
