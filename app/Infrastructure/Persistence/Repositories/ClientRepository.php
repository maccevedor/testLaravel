<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class ClientRepository implements ClientRepositoryInterface
{
    public function findById(int $id): ?Client
    {
        $clientData = DB::table('clients')->find($id);

        if (!$clientData) {
            return null;
        }

        $client = new Client(
            $clientData->tenant_id,
            $clientData->name,
            $clientData->email,
            $clientData->phone,
            $clientData->active
        );

        $this->setPrivateProperty($client, 'id', $clientData->id);
        $this->setPrivateProperty($client, 'createdAt', new \DateTime($clientData->created_at));
        $this->setPrivateProperty($client, 'updatedAt', new \DateTime($clientData->updated_at));

        return $client;
    }

    public function findByTenantId(int $tenantId): array
    {
        $clients = [];
        $clientsData = DB::table('clients')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($clientsData as $clientData) {
            $client = new Client(
                $clientData->tenant_id,
                $clientData->name,
                $clientData->email,
                $clientData->phone,
                $clientData->active
            );

            $this->setPrivateProperty($client, 'id', $clientData->id);
            $this->setPrivateProperty($client, 'createdAt', new \DateTime($clientData->created_at));
            $this->setPrivateProperty($client, 'updatedAt', new \DateTime($clientData->updated_at));

            $clients[] = $client;
        }

        return $clients;
    }

    public function findAll(): array
    {
        $clients = [];
        $clientsData = DB::table('clients')->get();

        foreach ($clientsData as $clientData) {
            $client = new Client(
                $clientData->tenant_id,
                $clientData->name,
                $clientData->email,
                $clientData->phone,
                $clientData->active
            );

            $this->setPrivateProperty($client, 'id', $clientData->id);
            $this->setPrivateProperty($client, 'createdAt', new \DateTime($clientData->created_at));
            $this->setPrivateProperty($client, 'updatedAt', new \DateTime($clientData->updated_at));

            $clients[] = $client;
        }

        return $clients;
    }

    public function save(Client $client): void
    {
        $id = DB::table('clients')->insertGetId([
            'tenant_id' => $client->getTenantId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
            'active' => $client->isActive(),
            'created_at' => $client->getCreatedAt(),
            'updated_at' => $client->getUpdatedAt(),
        ]);

        $this->setPrivateProperty($client, 'id', $id);
    }

    public function update(Client $client): void
    {
        DB::table('clients')
            ->where('id', $client->getId())
            ->update([
                'name' => $client->getName(),
                'email' => $client->getEmail(),
                'phone' => $client->getPhone(),
                'active' => $client->isActive(),
                'updated_at' => $client->getUpdatedAt(),
            ]);
    }

    public function delete(int $id): void
    {
        DB::table('clients')->where('id', $id)->delete();
    }

    private function setPrivateProperty($object, string $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
