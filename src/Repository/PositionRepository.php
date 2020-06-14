<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;

class PositionRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Position::class;
    }

    public function findAllForCustomers(): array
    {
        return $this->findBy(['isStaff' => 0], ['name' => 'ASC']);
    }

    public function findAllForStaffs(): array
    {
        return $this->findBy(['isStaff' => 1], ['name' => 'ASC']);
    }

    public function save(Position $position, bool $flush = true): void
    {
        $this->saveEntity($position, $flush);
    }
}
