<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Persistence\ManagerRegistry;

class CustomerPositionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    public function save(Position $position, bool $flush = true): void
    {
        $this->saveEntity($position, $flush);
    }

    public function remove(Position $position, bool $flush = true): void
    {
        $this->removeEntity($position, $flush);
    }
}
