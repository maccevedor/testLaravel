<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Plan\Entities\Plan;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use App\Infrastructure\Http\Resources\PlanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PlanController extends Controller
{
    private PlanRepositoryInterface $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function index(): JsonResponse
    {
        $plans = $this->planRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($plans),
            'data' => PlanResource::collection($plans)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'active' => 'boolean'
            ]);

            $plan = new Plan(
                $validated['name'],
                $validated['description'],
                $validated['price'],
                $validated['active'] ?? true
            );

            $this->planRepository->save($plan);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'data' => new PlanResource($plan)
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the plan'
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $plan = $this->planRepository->findById($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PlanResource($plan)
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $plan = $this->planRepository->findById($id);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'active' => 'boolean'
            ]);

            $plan->update(
                $validated['name'],
                $validated['description'],
                $validated['price'],
                $validated['active'] ?? true
            );

            $this->planRepository->update($plan);

            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully',
                'data' => new PlanResource($plan)
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the plan'
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $plan = $this->planRepository->findById($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $this->planRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully'
        ]);
    }
}
