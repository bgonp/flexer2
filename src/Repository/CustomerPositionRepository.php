<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CustomerPosition;
use Doctrine\Persistence\ManagerRegistry;

class CustomerPositionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerPosition::class);
    }

    public function save(CustomerPosition $position, bool $flush = true): void
    {
        $this->saveEntity($position, $flush);
    }

    public function remove(CustomerPosition $position, bool $flush = true): void
    {
        $this->removeEntity($position, $flush);
    }
}
