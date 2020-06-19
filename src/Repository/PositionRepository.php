<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Persistence\ManagerRegistry;

class PositionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    /** @return Position[] */
    public function findAllForCustomers(): array
    {
        return $this->findBy(['isStaff' => 0], ['name' => 'ASC']);
    }

    /** @return Position[] */
    public function findAllForStaffs(): array
    {
        return $this->findBy(['isStaff' => 1], ['name' => 'ASC']);
    }

    public function save(Position $position, bool $flush = true): void
    {
        $this->saveEntity($position, $flush);
    }
}
