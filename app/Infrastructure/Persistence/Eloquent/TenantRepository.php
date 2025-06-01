<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Models\Tenant as TenantModel;

class TenantRepository implements TenantRepositoryInterface
{
    public function findAll(): array
    {
        return TenantModel::all()->map(fn ($model) => $this->toEntity($model))->all();
    }

    public function findById(int $id): ?Tenant
    {
        $model = TenantModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function save(Tenant $tenant): void
    {
        $model = new TenantModel([
            'name' => $tenant->getName(),
            'domain' => $tenant->getDomain(),
            'active' => $tenant->isActive(),
        ]);

        $model->save();

        $reflection = new \ReflectionClass($tenant);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($tenant, $model->id);
    }

    public function update(Tenant $tenant): void
    {
        TenantModel::where('id', $tenant->getId())->update([
            'name' => $tenant->getName(),
            'domain' => $tenant->getDomain(),
            'active' => $tenant->isActive(),
        ]);
    }

    public function delete(int $id): void
    {
        TenantModel::destroy($id);
    }

    private function toEntity(TenantModel $model): Tenant
    {
        $tenant = new Tenant(
            $model->name,
            $model->domain,
            $model->active
        );

        $reflection = new \ReflectionClass($tenant);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($tenant, $model->id);

        return $tenant;
    }
}
