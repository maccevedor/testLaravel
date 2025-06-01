<?php

use App\Domain\Plan\Entities\Plan;
use App\Domain\Plan\Repositories\PlanRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    $this->planRepository = Mockery::mock(PlanRepositoryInterface::class);
    app()->instance(PlanRepositoryInterface::class, $this->planRepository);
});

test('index returns all plans', function () {
    $plans = [
        new Plan('Basic Plan', 'Basic features', 29.99),
        new Plan('Premium Plan', 'Premium features', 49.99),
    ];

    // Set IDs using reflection
    foreach ($plans as $index => $plan) {
        $reflection = new ReflectionClass($plan);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($plan, $index + 1);
    }

    $this->planRepository
        ->shouldReceive('findAll')
        ->once()
        ->andReturn($plans);

    $response = $this->getJson('/api/plans');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'count' => 2,
        ])
        ->assertJsonStructure([
            'success',
            'count',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('store creates a new plan', function () {
    $plan = new Plan('Basic Plan', 'Basic features', 29.99);

    $this->planRepository
        ->shouldReceive('save')
        ->once()
        ->andReturnUsing(function ($savedPlan) {
            $reflection = new ReflectionClass($savedPlan);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($savedPlan, 1);
        });

    $response = $this->postJson('/api/plans', [
        'name' => 'Basic Plan',
        'description' => 'Basic features',
        'price' => 29.99,
        'active' => true,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Plan created successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('show returns a specific plan', function () {
    $plan = new Plan('Basic Plan', 'Basic features', 29.99);
    $reflection = new ReflectionClass($plan);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($plan, 1);

    $this->planRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($plan);

    $response = $this->getJson('/api/plans/1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('show returns 404 for non-existent plan', function () {
    $this->planRepository
        ->shouldReceive('findById')
        ->with(999)
        ->once()
        ->andReturn(null);

    $response = $this->getJson('/api/plans/999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Plan not found',
        ]);
});

test('update modifies an existing plan', function () {
    $plan = new Plan('Basic Plan', 'Basic features', 29.99);
    $reflection = new ReflectionClass($plan);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($plan, 1);

    $this->planRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($plan);

    $this->planRepository
        ->shouldReceive('update')
        ->once();

    $response = $this->putJson('/api/plans/1', [
        'name' => 'Updated Plan',
        'description' => 'Updated features',
        'price' => 39.99,
        'active' => true,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Plan updated successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'active',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('destroy deletes a plan', function () {
    $plan = new Plan('Basic Plan', 'Basic features', 29.99);
    $reflection = new ReflectionClass($plan);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($plan, 1);

    $this->planRepository
        ->shouldReceive('findById')
        ->with(1)
        ->once()
        ->andReturn($plan);

    $this->planRepository
        ->shouldReceive('delete')
        ->with(1)
        ->once();

    $response = $this->deleteJson('/api/plans/1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
});
