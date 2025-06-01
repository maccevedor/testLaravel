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

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel API",
 *     description="API documentation for Laravel application",
 * )
 */
class ClientController extends Controller
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly TenantRepositoryInterface $tenantRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="List all clients",
     *     @OA\Response(response="200", description="Successful operation"),
     * )
     */
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

        $this->authorize('viewAny', Client::class);

        return response()->json([
            'success' => true,
            'count' => count($clients),
            'data' => ClientResource::collection($clients)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     summary="Get a specific client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="404", description="Client not found"),
     * )
     */
    public function show(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $this->authorize('view', $client);

        return response()->json([
            'success' => true,
            'data' => new ClientResource($client)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/clients",
     *     summary="Create a new client",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tenant_id", "name", "email", "phone"},
     *             @OA\Property(property="tenant_id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Client created successfully"),
     *     @OA\Response(response="404", description="Tenant not found"),
     * )
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $this->authorize('create', Client::class);

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

    /**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     summary="Update a client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Client updated successfully"),
     *     @OA\Response(response="404", description="Client not found"),
     * )
     */
    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $this->authorize('update', $client);

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

    /**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     summary="Delete a client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Client deleted successfully"),
     *     @OA\Response(response="404", description="Client not found"),
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $this->authorize('delete', $client);

        $this->clientRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    }
}
