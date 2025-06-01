<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Repositories\TenantRepositoryInterface;
use App\Infrastructure\Http\Resources\TenantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class TenantController extends Controller
{
    private TenantRepositoryInterface $tenantRepository;

    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    public function index(): JsonResponse
    {
        $tenants = $this->tenantRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($tenants),
            'data' => TenantResource::collection($tenants)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'domain' => 'required|string|max:255',
                'active' => 'boolean'
            ]);

            $tenant = new Tenant(
                $validated['name'],
                $validated['email'],
                $validated['domain'],
                $validated['active'] ?? true
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

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'domain' => 'required|string|max:255',
                'active' => 'boolean'
            ]);

            $tenant->update(
                $validated['name'],
                $validated['email'],
                $validated['domain'],
                $validated['active'] ?? true
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
        ], 200);
    }
}
