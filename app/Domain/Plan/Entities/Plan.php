<?php

namespace App\Domain\Plan\Entities;

class Plan
{
    private ?int $id;
    private string $name;
    private string $description;
    private float $price;
    private bool $active;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        string $name,
        string $description,
        float $price,
        bool $active = true
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->active = $active;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function update(string $name, string $description, float $price, bool $active): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->active = $active;
        $this->updatedAt = new \DateTime();
    }
}
