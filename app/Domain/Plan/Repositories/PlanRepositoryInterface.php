<?php

namespace App\Domain\Plan\Repositories;

use App\Domain\Plan\Entities\Plan;

interface PlanRepositoryInterface
{
    public function findById(int $id): ?Plan;
    public function findAll(): array;
    public function save(Plan $plan): void;
    public function update(Plan $plan): void;
    public function delete(int $id): void;
}
