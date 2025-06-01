<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infrastructure\Http\Requests\Client\StoreClientRequest;
use App\Infrastructure\Http\Requests\Client\UpdateClientRequest;
use App\Infrastructure\Http\Resources\ClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly TenantRepositoryInterface $tenantRepository
    ) {}

    public function index(): JsonResponse
    {
        $tenantId = request()->query('tenant_id');

        if ($tenantId) {
            $tenant = $this->tenantRepository->findById($tenantId);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            $clients = $this->clientRepository->findByTenantId($tenantId);
        } else {
            $clients = $this->clientRepository->findAll();
        }

        return response()->json([
            'success' => true,
            'count' => count($clients),
            'data' => ClientResource::collection($clients)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ClientResource($client)
        ]);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $tenant = $this->tenantRepository->findById($request->validated('tenant_id'));
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $client = new Client(
            $request->validated('tenant_id'),
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('phone'),
            $request->validated('active', true)
        );

        $this->clientRepository->save($client);

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'data' => new ClientResource($client)
        ], 201);
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $client->update(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('phone'),
            $request->validated('active', true)
        );

        $this->clientRepository->update($client);

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => new ClientResource($client)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $this->clientRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    }
}
