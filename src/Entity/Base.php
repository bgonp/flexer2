<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;

abstract class Base
{
    protected string $id;

    protected \DateTime $createdAt;

    protected \DateTime $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime();
        $this->updateNow();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function updateNow(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function equals(?Base $object): bool
    {
        return $object && get_called_class() === get_class($object) && $this->getId() === $object->getId();
    }
}
