<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class TenantRepository implements TenantRepositoryInterface
{
    public function findById(int $id): ?Tenant
    {
        $tenantData = DB::table('tenants')->find($id);

        if (!$tenantData) {
            return null;
        }

        $tenant = new Tenant(
            $tenantData->name,
            $tenantData->email,
            $tenantData->domain,
            $tenantData->active
        );

        $this->setPrivateProperty($tenant, 'id', $tenantData->id);
        $this->setPrivateProperty($tenant, 'createdAt', new \DateTime($tenantData->created_at));
        $this->setPrivateProperty($tenant, 'updatedAt', new \DateTime($tenantData->updated_at));

        return $tenant;
    }

    public function findAll(): array
    {
        $tenants = [];
        $tenantsData = DB::table('tenants')->get();

        foreach ($tenantsData as $tenantData) {
            $tenant = new Tenant(
                $tenantData->name,
                $tenantData->email,
                $tenantData->domain,
                $tenantData->active
            );

            $this->setPrivateProperty($tenant, 'id', $tenantData->id);
            $this->setPrivateProperty($tenant, 'createdAt', new \DateTime($tenantData->created_at));
            $this->setPrivateProperty($tenant, 'updatedAt', new \DateTime($tenantData->updated_at));

            $tenants[] = $tenant;
        }

        return $tenants;
    }

    public function save(Tenant $tenant): void
    {
        $id = DB::table('tenants')->insertGetId([
            'name' => $tenant->getName(),
            'email' => $tenant->getEmail(),
            'domain' => $tenant->getDomain(),
            'active' => $tenant->isActive(),
            'created_at' => $tenant->getCreatedAt(),
            'updated_at' => $tenant->getUpdatedAt(),
        ]);

        $this->setPrivateProperty($tenant, 'id', $id);
    }

    public function update(Tenant $tenant): void
    {
        DB::table('tenants')
            ->where('id', $tenant->getId())
            ->update([
                'name' => $tenant->getName(),
                'email' => $tenant->getEmail(),
                'domain' => $tenant->getDomain(),
                'active' => $tenant->isActive(),
                'updated_at' => $tenant->getUpdatedAt(),
            ]);
    }

    public function delete(int $id): void
    {
        DB::table('tenants')->where('id', $id)->delete();
    }

    private function setPrivateProperty($object, string $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
