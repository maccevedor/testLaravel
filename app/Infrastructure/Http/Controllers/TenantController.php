<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infrastructure\Http\Requests\Tenant\StoreTenantRequest;
use App\Infrastructure\Http\Requests\Tenant\UpdateTenantRequest;
use App\Infrastructure\Http\Resources\TenantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\QueryException;

class TenantController extends Controller
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/tenants",
     *     summary="List all tenants",
     *     @OA\Response(response="200", description="Successful operation"),
     * )
     */
    public function index(): JsonResponse
    {
        $tenants = $this->tenantRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($tenants),
            'data' => TenantResource::collection($tenants)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/tenants/{id}",
     *     summary="Get a specific tenant",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="404", description="Tenant not found"),
     * )
     */
    public function show(int $id): JsonResponse
    {
        $tenant = $this->tenantRepository->findById($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TenantResource($tenant)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/tenants",
     *     summary="Create a new tenant",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "domain", "plan_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="domain", type="string"),
     *             @OA\Property(property="plan_id", type="integer"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Tenant created successfully"),
     * )
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        try {
            $tenant = new Tenant(
                $request->validated('name'),
                $request->validated('email'),
                $request->validated('domain'),
                $request->validated('plan_id'),
                $request->validated('active', true)
            );

            $this->tenantRepository->save($tenant);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => new TenantResource($tenant)
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'tenants_domain_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Domain already exists'
                    ], 422);
                }
                if (str_contains($e->getMessage(), 'tenants_email_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists'
                    ], 422);
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the tenant'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/tenants/{id}",
     *     summary="Update a tenant",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "domain", "plan_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="domain", type="string"),
     *             @OA\Property(property="plan_id", type="integer"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Tenant updated successfully"),
     *     @OA\Response(response="404", description="Tenant not found"),
     * )
     */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $tenant->update(
                $request->validated('name'),
                $request->validated('email'),
                $request->validated('domain'),
                $request->validated('plan_id'),
                $request->validated('active', true)
            );

            $this->tenantRepository->update($tenant);

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'data' => new TenantResource($tenant)
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'tenants_domain_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Domain already exists'
                    ], 422);
                }
                if (str_contains($e->getMessage(), 'tenants_email_unique')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists'
                    ], 422);
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the tenant'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tenants/{id}",
     *     summary="Delete a tenant",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Tenant deleted successfully"),
     *     @OA\Response(response="404", description="Tenant not found"),
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $tenant = $this->tenantRepository->findById($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $this->tenantRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully'
        ]);
    }
}
