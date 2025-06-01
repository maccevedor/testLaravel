<?php

namespace App\Domain\Client\Entities;

class Client
{
    private ?int $id;
    private int $tenantId;
    private string $name;
    private string $email;
    private string $phone;
    private bool $active;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        int $tenantId,
        string $name,
        string $email,
        string $phone,
        bool $active = true
    ) {
        $this->tenantId = $tenantId;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->active = $active;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
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

    public function update(string $name, string $email, string $phone, bool $active): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->active = $active;
        $this->updatedAt = new \DateTime();
    }
}
