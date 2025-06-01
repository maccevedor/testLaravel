<?php

use App\Domain\Tenant\Entities\Tenant;

test('tenant can be created with required attributes', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com',
        1,
        true
    );

    expect($tenant)
        ->getName()->toBe('Acme Corp')
        ->getEmail()->toBe('acme@example.com')
        ->getDomain()->toBe('acme.example.com')
        ->getPlanId()->toBe(1)
        ->isActive()->toBeTrue();
});

test('tenant can be created without plan', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com'
    );

    expect($tenant)
        ->getName()->toBe('Acme Corp')
        ->getEmail()->toBe('acme@example.com')
        ->getDomain()->toBe('acme.example.com')
        ->getPlanId()->toBeNull()
        ->isActive()->toBeTrue();
});

test('tenant can be updated', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com',
        1
    );

    $tenant->update(
        'Acme Corp Updated',
        'acme.updated@example.com',
        'acme.updated.example.com',
        2,
        true
    );

    expect($tenant)
        ->getName()->toBe('Acme Corp Updated')
        ->getEmail()->toBe('acme.updated@example.com')
        ->getDomain()->toBe('acme.updated.example.com')
        ->getPlanId()->toBe(2)
        ->isActive()->toBeTrue();
});

test('tenant can assign plan', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com'
    );

    $tenant->assignPlan(1);

    expect($tenant)
        ->getPlanId()->toBe(1);
});

test('tenant timestamps are set on creation', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com'
    );

    expect($tenant)
        ->getCreatedAt()->toBeInstanceOf(\DateTime::class)
        ->getUpdatedAt()->toBeInstanceOf(\DateTime::class);
});

test('tenant timestamps are updated on update', function () {
    $tenant = new Tenant(
        'Acme Corp',
        'acme@example.com',
        'acme.example.com'
    );

    $originalUpdatedAt = $tenant->getUpdatedAt();

    // Wait a moment to ensure timestamp difference
    usleep(1000000); // 1 second

    $tenant->update(
        'Acme Corp Updated',
        'acme.updated@example.com',
        'acme.updated.example.com',
        2,
        true
    );

    expect($tenant->getUpdatedAt())
        ->toBeInstanceOf(\DateTime::class)
        ->not->toEqual($originalUpdatedAt);
});
