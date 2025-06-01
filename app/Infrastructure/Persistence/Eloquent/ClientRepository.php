<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Models\Client as ClientModel;

class ClientRepository implements ClientRepositoryInterface
{
    public function findAll(): array
    {
        return ClientModel::all()->map(fn ($model) => $this->toEntity($model))->all();
    }

    public function findById(int $id): ?Client
    {
        $model = ClientModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByTenantId(int $tenantId): array
    {
        return ClientModel::where('tenant_id', $tenantId)
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->all();
    }

    public function save(Client $client): void
    {
        $model = new ClientModel([
            'tenant_id' => $client->getTenantId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
            'active' => $client->isActive(),
        ]);

        $model->save();

        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($client, $model->id);
    }

    public function update(Client $client): void
    {
        ClientModel::where('id', $client->getId())->update([
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
            'active' => $client->isActive(),
        ]);
    }

    public function delete(int $id): void
    {
        ClientModel::destroy($id);
    }

    private function toEntity(ClientModel $model): Client
    {
        $client = new Client(
            $model->tenant_id,
            $model->name,
            $model->email,
            $model->phone,
            $model->active
        );

        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($client, $model->id);

        return $client;
    }
}
