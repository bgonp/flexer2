<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Persistence\ManagerRegistry;

class RateRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function save(Rate $rate, bool $flush = true)
    {
        $this->saveEntity($rate, $flush);
    }
}
