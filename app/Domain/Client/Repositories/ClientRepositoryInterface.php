<?php

namespace App\Domain\Client\Repositories;

use App\Domain\Client\Entities\Client;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?Client;
    public function findByTenantId(int $tenantId): array;
    public function findAll(): array;
    public function save(Client $client): void;
    public function update(Client $client): void;
    public function delete(int $id): void;
}
