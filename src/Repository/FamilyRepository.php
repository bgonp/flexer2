<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Family;
use Doctrine\Persistence\ManagerRegistry;

class FamilyRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Family::class);
    }

    public function save(Family $family, bool $flush = true): void
    {
        $this->saveEntity($family, $flush);
    }

    public function remove(Family $family, bool $flush = true): void
    {
        $this->removeEntity($family, $flush);
    }
}
