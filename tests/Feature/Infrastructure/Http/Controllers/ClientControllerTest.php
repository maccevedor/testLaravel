<?php

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
    $this->tenantRepository = Mockery::mock(TenantRepositoryInterface::class);
    app()->instance(ClientRepositoryInterface::class, $this->clientRepository);
    app()->instance(TenantRepositoryInterface::class, $this->tenantRepository);
});

test('index returns all clients when no tenant_id provided', function () {
    $clients = [
        new Client(1, 'John Doe', 'john@example.com', '+1234567890'),
        new Client(2, 'Jane Doe', 'jane@example.com', '+1987654321'),
    ];

    // Set IDs using reflection
    foreach ($clients as $index => $client) {
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($client, $index + 1);
    }

    $this->clientRepository
        ->shouldReceive('findAll')
        ->once()
        ->andReturn($clients);

    $response = $this->getJson('/api/clients');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'count' => 2,
        ])
        ->assertJsonStructure([
            'success',
            'count',
            'data' => [
                '*' => [
                    'id',
                    'tenant_id',
                    'name',
                    'email',
                    'phone',
                    'active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('index returns clients for specific tenant', function () {
    $tenant = new Tenant('Acme Corp', 'acme@example.com', 'acme.example.com');
    $clients = [
        new Client(1, 'John Doe', 'john@example.com', '+1234567890'),
        new Client(1, 'Jane Doe', 'jane@example.com', '+1987654321'),
    ];

    // Set IDs using reflection
    foreach ($clients as $index => $client) {
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($client, $index + 1);
    }

    $this->tenantRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($tenant);

    $this->clientRepository
        ->shouldReceive('findByTenantId')
        ->with(1)
        ->once()
        ->andReturn($clients);

    $response = $this->getJson('/api/clients?tenant_id=1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'count' => 2,
        ])
        ->assertJsonStructure([
            'success',
            'count',
            'data' => [
                '*' => [
                    'id',
                    'tenant_id',
                    'name',
                    'email',
                    'phone',
                    'active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('index returns 404 for non-existent tenant', function () {
    $this->tenantRepository
        ->shouldReceive('findById')
        ->with(999)
        ->once()
        ->andReturn(null);

    $response = $this->getJson('/api/clients?tenant_id=999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tenant not found',
        ]);
});

test('store creates a new client', function () {
    $tenant = new Tenant('Acme Corp', 'acme@example.com', 'acme.example.com');
    $client = new Client(1, 'John Doe', 'john@example.com', '+1234567890');

    $this->tenantRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($tenant);

    $this->clientRepository
        ->shouldReceive('save')
        ->once()
        ->andReturnUsing(function ($savedClient) {
            $reflection = new ReflectionClass($savedClient);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($savedClient, 1);
        });

    $response = $this->postJson('/api/clients', [
        'tenant_id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'active' => true,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Client created successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'tenant_id',
                'name',
                'email',
                'phone',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('store returns 404 for non-existent tenant', function () {
    $this->tenantRepository
        ->shouldReceive('findById')
        ->with(999)
        ->once()
        ->andReturn(null);

    $response = $this->postJson('/api/clients', [
        'tenant_id' => 999,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'active' => true,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tenant not found',
        ]);
});

test('show returns a specific client', function () {
    $client = new Client(1, 'John Doe', 'john@example.com', '+1234567890');
    $reflection = new ReflectionClass($client);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($client, 1);

    $this->clientRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($client);

    $response = $this->getJson('/api/clients/1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'tenant_id',
                'name',
                'email',
                'phone',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('show returns 404 for non-existent client', function () {
    $this->clientRepository
        ->shouldReceive('findById')
        ->with(999)
        ->once()
        ->andReturn(null);

    $response = $this->getJson('/api/clients/999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Client not found',
        ]);
});

test('update modifies an existing client', function () {
    $client = new Client(1, 'John Doe', 'john@example.com', '+1234567890');
    $reflection = new ReflectionClass($client);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($client, 1);

    $this->clientRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($client);

    $this->clientRepository
        ->shouldReceive('update')
        ->once();

    $response = $this->putJson('/api/clients/1', [
        'name' => 'John Doe Updated',
        'email' => 'john.updated@example.com',
        'phone' => '+1987654321',
        'active' => true,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Client updated successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'tenant_id',
                'name',
                'email',
                'phone',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('destroy deletes a client', function () {
    $client = new Client(1, 'John Doe', 'john@example.com', '+1234567890');
    $reflection = new ReflectionClass($client);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($client, 1);

    $this->clientRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($client);

    $this->clientRepository
        ->shouldReceive('delete')
        ->with(1)
        ->once();

    $response = $this->deleteJson('/api/clients/1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Client deleted successfully',
        ]);
});
