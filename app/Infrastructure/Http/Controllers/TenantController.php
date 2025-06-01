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

    public function index(): JsonResponse
    {
        $tenants = $this->tenantRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($tenants),
            'data' => TenantResource::collection($tenants)
        ]);
    }

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
