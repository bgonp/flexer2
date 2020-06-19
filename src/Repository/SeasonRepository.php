<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Persistence\ManagerRegistry;

class SeasonRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function save(Season $season, bool $flush = true): void
    {
        $this->saveEntity($season, $flush);
    }
}
