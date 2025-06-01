<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Plan\Entities\Plan;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use App\Models\Plan as PlanModel;

class PlanRepository implements PlanRepositoryInterface
{
    public function findAll(): array
    {
        return PlanModel::all()->map(fn ($model) => $this->toEntity($model))->all();
    }

    public function findById(int $id): ?Plan
    {
        $model = PlanModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function save(Plan $plan): void
    {
        $model = new PlanModel([
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'price' => $plan->getPrice(),
            'active' => $plan->isActive(),
        ]);

        $model->save();

        $reflection = new \ReflectionClass($plan);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($plan, $model->id);
    }

    public function update(Plan $plan): void
    {
        PlanModel::where('id', $plan->getId())->update([
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'price' => $plan->getPrice(),
            'active' => $plan->isActive(),
        ]);
    }

    public function delete(int $id): void
    {
        PlanModel::destroy($id);
    }

    private function toEntity(PlanModel $model): Plan
    {
        $plan = new Plan(
            $model->name,
            $model->description,
            $model->price,
            $model->active
        );

        $reflection = new \ReflectionClass($plan);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($plan, $model->id);

        return $plan;
    }
}
