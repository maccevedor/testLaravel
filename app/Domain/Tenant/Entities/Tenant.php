<?php

namespace App\Domain\Tenant\Entities;

class Tenant
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $domain;
    private ?int $planId;
    private bool $active;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        string $name,
        string $email,
        string $domain,
        ?int $planId = null,
        bool $active = true
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->domain = $domain;
        $this->planId = $planId;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getPlanId(): ?int
    {
        return $this->planId;
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

    public function update(string $name, string $email, string $domain, ?int $planId, bool $active): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->domain = $domain;
        $this->planId = $planId;
        $this->active = $active;
        $this->updatedAt = new \DateTime();
    }

    public function assignPlan(?int $planId): void
    {
        $this->planId = $planId;
        $this->updatedAt = new \DateTime();
    }
}
