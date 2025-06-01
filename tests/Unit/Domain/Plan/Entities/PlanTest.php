<?php

use App\Domain\Plan\Entities\Plan;

test('plan can be created with required attributes', function () {
    $plan = new Plan(
        'Basic Plan',
        'Basic features for small businesses',
        29.99,
        true
    );

    expect($plan)
        ->getName()->toBe('Basic Plan')
        ->getDescription()->toBe('Basic features for small businesses')
        ->getPrice()->toBe(29.99)
        ->isActive()->toBeTrue();
});

test('plan can be updated', function () {
    $plan = new Plan(
        'Basic Plan',
        'Basic features for small businesses',
        29.99,
        true
    );

    $plan->update(
        'Premium Plan',
        'Premium features for businesses',
        49.99,
        true
    );

    expect($plan)
        ->getName()->toBe('Premium Plan')
        ->getDescription()->toBe('Premium features for businesses')
        ->getPrice()->toBe(49.99)
        ->isActive()->toBeTrue();
});

test('plan timestamps are set on creation', function () {
    $plan = new Plan(
        'Basic Plan',
        'Basic features for small businesses',
        29.99
    );

    expect($plan)
        ->getCreatedAt()->toBeInstanceOf(\DateTime::class)
        ->getUpdatedAt()->toBeInstanceOf(\DateTime::class);
});

test('plan timestamps are updated on update', function () {
    $plan = new Plan(
        'Basic Plan',
        'Basic features for small businesses',
        29.99
    );

    $originalUpdatedAt = $plan->getUpdatedAt();

    // Wait a moment to ensure timestamp difference
    usleep(1000000); // 1 second

    $plan->update(
        'Premium Plan',
        'Premium features for businesses',
        49.99,
        true
    );

    expect($plan->getUpdatedAt())
        ->toBeInstanceOf(\DateTime::class)
        ->not->toEqual($originalUpdatedAt);
});
