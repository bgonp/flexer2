<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StaffPosition;
use Doctrine\Persistence\ManagerRegistry;

class StaffPositionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaffPosition::class);
    }

    public function save(StaffPosition $position, bool $flush = true): void
    {
        $this->saveEntity($position, $flush);
    }

    public function remove(StaffPosition $position, bool $flush = true): void
    {
        $this->removeEntity($position, $flush);
    }
}
