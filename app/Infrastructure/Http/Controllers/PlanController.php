<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Plan\Entities\Plan;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use App\Infrastructure\Http\Requests\Plan\StorePlanRequest;
use App\Infrastructure\Http\Requests\Plan\UpdatePlanRequest;
use App\Infrastructure\Http\Resources\PlanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanRepositoryInterface $planRepository
    ) {}

    public function index(): JsonResponse
    {
        $plans = $this->planRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($plans),
            'data' => PlanResource::collection($plans)
        ]);
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

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = new Plan(
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('price'),
            $request->validated('active', true)
        );

        $this->planRepository->save($plan);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => new PlanResource($plan)
        ], 201);
    }

    public function update(UpdatePlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->planRepository->findById($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $plan->update(
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('price'),
            $request->validated('active', true)
        );

        $this->planRepository->update($plan);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => new PlanResource($plan)
        ]);
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
