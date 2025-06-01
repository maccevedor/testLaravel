<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Plan\Entities\Plan;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class PlanRepository implements PlanRepositoryInterface
{
    public function findById(int $id): ?Plan
    {
        $planData = DB::table('plans')->find($id);

        if (!$planData) {
            return null;
        }

        $plan = new Plan(
            $planData->name,
            $planData->description,
            $planData->price,
            $planData->active
        );

        $this->setPrivateProperty($plan, 'id', $planData->id);
        $this->setPrivateProperty($plan, 'createdAt', new \DateTime($planData->created_at));
        $this->setPrivateProperty($plan, 'updatedAt', new \DateTime($planData->updated_at));

        return $plan;
    }

    public function findAll(): array
    {
        $plans = [];
        $plansData = DB::table('plans')->get();

        foreach ($plansData as $planData) {
            $plan = new Plan(
                $planData->name,
                $planData->description,
                $planData->price,
                $planData->active
            );

            $this->setPrivateProperty($plan, 'id', $planData->id);
            $this->setPrivateProperty($plan, 'createdAt', new \DateTime($planData->created_at));
            $this->setPrivateProperty($plan, 'updatedAt', new \DateTime($planData->updated_at));

            $plans[] = $plan;
        }

        return $plans;
    }

    public function save(Plan $plan): void
    {
        $id = DB::table('plans')->insertGetId([
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'price' => $plan->getPrice(),
            'active' => $plan->isActive(),
            'created_at' => $plan->getCreatedAt(),
            'updated_at' => $plan->getUpdatedAt(),
        ]);

        $this->setPrivateProperty($plan, 'id', $id);
    }

    public function update(Plan $plan): void
    {
        DB::table('plans')
            ->where('id', $plan->getId())
            ->update([
                'name' => $plan->getName(),
                'description' => $plan->getDescription(),
                'price' => $plan->getPrice(),
                'active' => $plan->isActive(),
                'updated_at' => $plan->getUpdatedAt(),
            ]);
    }

    public function delete(int $id): void
    {
        DB::table('plans')->where('id', $id)->delete();
    }

    private function setPrivateProperty($object, string $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
