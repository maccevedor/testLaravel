<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Http\Resources\ClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ClientController extends Controller
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->query('tenant_id');

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant ID is required'
            ], 400);
        }

        $clients = $this->clientRepository->findByTenantId($tenantId);
        return response()->json([
            'success' => true,
            'count' => count($clients),
            'data' => ClientResource::collection($clients)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'required|integer|exists:tenants,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'active' => 'boolean'
            ]);

            $client = new Client(
                $validated['tenant_id'],
                $validated['name'],
                $validated['email'],
                $validated['phone'],
                $validated['active'] ?? true
            );

            $this->clientRepository->save($client);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => new ClientResource($client)
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'clients_tenant_id_email_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists for this tenant'
                    ], 422);
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the client'
            ], 500);
        }
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

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientRepository->findById($id);

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'active' => 'boolean'
            ]);

            $client->update(
                $validated['name'],
                $validated['email'],
                $validated['phone'],
                $validated['active'] ?? true
            );

            $this->clientRepository->update($client);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully',
                'data' => new ClientResource($client)
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'clients_tenant_id_email_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists for this tenant'
                    ], 422);
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the client'
            ], 500);
        }
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
