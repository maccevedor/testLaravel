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

    /**
     * @OA\Get(
     *     path="/api/plans",
     *     summary="List all plans",
     *     @OA\Response(response="200", description="Successful operation"),
     * )
     */
    public function index(): JsonResponse
    {
        $plans = $this->planRepository->findAll();
        return response()->json([
            'success' => true,
            'count' => count($plans),
            'data' => PlanResource::collection($plans)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/plans/{id}",
     *     summary="Get a specific plan",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="404", description="Plan not found"),
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/plans",
     *     summary="Create a new plan",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "description"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Plan created successfully"),
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/plans/{id}",
     *     summary="Update a plan",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "description"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Plan updated successfully"),
     *     @OA\Response(response="404", description="Plan not found"),
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/plans/{id}",
     *     summary="Delete a plan",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Plan deleted successfully"),
     *     @OA\Response(response="404", description="Plan not found"),
     * )
     */
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
