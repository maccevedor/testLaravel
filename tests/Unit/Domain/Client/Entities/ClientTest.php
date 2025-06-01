<?php

use App\Domain\Client\Entities\Client;

test('client can be created with required attributes', function () {
    $client = new Client(
        1,
        'John Doe',
        'john@example.com',
        '+1234567890',
        true
    );

    expect($client)
        ->getTenantId()->toBe(1)
        ->getName()->toBe('John Doe')
        ->getEmail()->toBe('john@example.com')
        ->getPhone()->toBe('+1234567890')
        ->isActive()->toBeTrue();
});

test('client can be updated', function () {
    $client = new Client(
        1,
        'John Doe',
        'john@example.com',
        '+1234567890',
        true
    );

    $client->update(
        'John Doe Updated',
        'john.updated@example.com',
        '+1987654321',
        true
    );

    expect($client)
        ->getName()->toBe('John Doe Updated')
        ->getEmail()->toBe('john.updated@example.com')
        ->getPhone()->toBe('+1987654321')
        ->isActive()->toBeTrue();
});

test('client timestamps are set on creation', function () {
    $client = new Client(
        1,
        'John Doe',
        'john@example.com',
        '+1234567890'
    );

    expect($client)
        ->getCreatedAt()->toBeInstanceOf(\DateTime::class)
        ->getUpdatedAt()->toBeInstanceOf(\DateTime::class);
});

test('client timestamps are updated on update', function () {
    $client = new Client(
        1,
        'John Doe',
        'john@example.com',
        '+1234567890'
    );

    $originalUpdatedAt = $client->getUpdatedAt();

    // Wait a moment to ensure timestamp difference
    usleep(1000000); // 1 second

    $client->update(
        'John Doe Updated',
        'john.updated@example.com',
        '+1987654321',
        true
    );

    expect($client->getUpdatedAt())
        ->toBeInstanceOf(\DateTime::class)
        ->not->toEqual($originalUpdatedAt);
});
